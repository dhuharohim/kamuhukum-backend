<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleBackboneRequest;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleCommentAttachment;
use App\Models\ArticleContributors;
use App\Models\ArticleFile;
use App\Models\ArticleKeyword;
use App\Models\ArticleReference;
use App\Models\Edition;
use App\Notifications\NewCommentNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ArticleController extends Controller
{
    private $articleFor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->articleFor = $user->hasRole(['admin_law', 'editor_law']) ? 'law' : 'economic';

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index($editionId)
    {
        $edition = self::checkEdition($editionId);
        if (!$edition) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        $articles = Article::where('edition_id', $editionId)
            ->with(['authors'])
            ->where('article_for', $this->articleFor)
            ->orderBy('created_at', 'ASC')
            ->get();

        return view('Contents.articles.list')->with([
            'edition' => $edition,
            'articles' => $articles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($editionId)
    {
        $edition = self::checkEdition($editionId);
        if (!$edition) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        return view('Contents.articles.create')->with(['edition' => $edition]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($editionId, ArticleBackboneRequest $request)
    {
        $edition = self::checkEdition($editionId);
        if (!$edition) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        // if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
        //     return redirect()->back()->with('message', 'Unauthorized');
        // }

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title . '-' . $this->articleFor);
        }

        $publishedDate = null;
        if ($request->status == 'production') {
            $publishedDate = date('Y-m-d');
        }

        DB::beginTransaction();
        try {
            $article = Article::create([
                // 'user_id',
                'edition_id' => $edition->id,
                'article_for' => $this->articleFor,
                'prefix' => $request->prefix,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'section' => $request->section,
                'status' => $request->status,
                // 'comments_for_editor' ,
                'abstract' => $request->abstract,
                'slug' => $slug,
                'viewed' => 0,
                'published_date' => $publishedDate
            ]);

            $article->update([
                'uuid' => Crypt::encrypt($article->id)
            ]);

            // article keywords
            $keywordsArray = explode(',', $request->keywords);
            if (count($keywordsArray) > 0) {
                foreach ($keywordsArray as $keyword) {
                    ArticleKeyword::create([
                        'article_id' => $article->id,
                        'keyword' => $keyword
                    ]);
                }
            }

            // article references
            if (isset($request->references) && count($request->references) > 0) {
                foreach ($request->references as $reference) {
                    ArticleReference::create([
                        'article_id' => $article->id,
                        'reference' => $reference
                    ]);
                }
            }

            // article contributors
            if (count($request->contributors) > 0) {
                foreach ($request->contributors as $contributor) {
                    ArticleContributors::create([
                        'article_id' => $article->id,
                        'contributor_role' => $contributor['role'],
                        'given_name' => $contributor['given_name'],
                        'family_name' => $contributor['family_name'],
                        'email' => $contributor['email'],
                        'phone' => $contributor['phone'],
                        'preferred_name' => $contributor['preferred_name'],
                        'affilation' => $contributor['affilation'],
                        'country' => $contributor['country'],
                        // 'img_url',
                        'homepage_url' => $contributor['homepage_url'],
                        'orcid_id' => $contributor['orcid_id'],
                        // 'mailing_address' => $contributor->,
                        'bio_statement' => $contributor['bio_statement'],
                        // 'reviewing_interest',
                        // 'principal_contact' => $contributor['principal_contact'] == 'on' ? '1' : '0',
                        // 'in_browse_list' => $contributor['in_browse_list'] == 'on' ? '1' : '0'
                    ]);
                }
            }

            // article files
            foreach ($request->article_files as $key => $file) {
                if ($file['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $uploadedFile = $file['file'];

                    // Generate the filename
                    $filename = 'file-' . $slug . '.' . $uploadedFile->getClientOriginalExtension();

                    // Store the file and get the storage path
                    $uploadedFile->storeAs('uploads/articles/' . $this->articleFor, $filename, 'public');

                    // Save file info in the database
                    ArticleFile::create([
                        'article_id' => $article->id, // Replace with actual article ID
                        'file_name' => $filename,
                        'file_path' => 'uploads/articles/' . $this->articleFor . '/' . $filename,
                        'type' => $file['type']
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error saving article: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('articles.index', $edition->id)->with('message', 'Article created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($editionId, string $id)
    {
        $edition = self::checkEdition($editionId);
        if (!$edition) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        $article = Article::where('edition_id', $edition->id)
            ->where('id', $id)
            ->with(['authors', 'keywords', 'files', 'references'])
            ->where('article_for', $this->articleFor)
            ->first();

        if (!$article) {
            return redirect()->back()->with('message', 'Article not found');
        }

        return view('Contents.articles.show')->with(['edition' => $edition, 'article' => $article]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($editionId, Request $request, string $id)
    {
        // dd($request->all());
        $edition = self::checkEdition($editionId);
        if (empty($edition)) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        // if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
        //     return redirect()->back()->with('message', 'Unauthorized');
        // }

        $article = Article::where('id', $id)
            ->where('edition_id', $editionId)
            ->where('article_for', $this->articleFor)
            ->first();

        if (empty($article)) {
            return redirect()->back()->with('message', 'Article not found');
        }

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title . '-' . $this->articleFor);
        }

        $publishedDate = null;
        if ($request->status == 'production') {
            $publishedDate = date('Y-m-d');
        }

        // dd($request->all());
        DB::beginTransaction();
        try {
            $article->update([
                'prefix' => $request->prefix,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'section' => $request->section,
                'status' => $request->status,
                // 'comments_for_editor' ,
                'abstract' => $request->abstract,
                'slug' => $slug,
                'published_date' => $publishedDate
            ]);

            // article keywords
            $keywordsArray = explode(',', $request->keywords);
            if (is_array($keywordsArray) && count($keywordsArray) > 0) {
                ArticleKeyword::where('article_id', $article->id)->forceDelete();
                foreach ($keywordsArray as $keyword) {
                    ArticleKeyword::create([
                        'article_id' => $article->id,
                        'keyword' => $keyword
                    ]);
                }
            }

            // article references
            if (is_array($request->references) && count($request->references) > 0) {
                ArticleReference::where('article_id', $article->id)->forceDelete();
                foreach ($request->references as $reference) {
                    ArticleReference::create([
                        'article_id' => $article->id,
                        'reference' => $reference
                    ]);
                }
            }

            // article contributors
            ArticleContributors::where('article_id', $article->id)->forceDelete();
            if (count($request->contributors) > 0) {
                foreach ($request->contributors as $contributor) {
                    ArticleContributors::create([
                        'article_id' => $article->id,
                        'contributor_role' => $contributor['role'],
                        'given_name' => $contributor['given_name'],
                        'family_name' => $contributor['family_name'],
                        'email' => $contributor['email'],
                        'phone' => $contributor['phone'],
                        'preferred_name' => $contributor['preferred_name'],
                        'affilation' => $contributor['affilation'],
                        'country' => $contributor['country'],
                        // 'img_url',
                        'homepage_url' => $contributor['homepage_url'],
                        'orcid_id' => $contributor['orcid_id'],
                        // 'mailing_address' => $contributor->,
                        'bio_statement' => $contributor['bio_statement'],
                        // 'reviewing_interest',
                        // 'principal_contact' => $contributor['principal_contact'] == 'on' ? '1' : '0',
                        // 'in_browse_list' => $contributor['in_browse_list'] == 'on' ? '1' : '0'
                    ]);
                }
            }

            // article files
            $exsistingFilesReq = $request->existing_files ?? [];
            $exsistingFiles = ArticleFile::where('article_id', $article->id)->get();
            if (count($exsistingFiles) > 0 && count($exsistingFilesReq) == 0) {
                $articleFiles = ArticleFile::where('article_id')->get();
                foreach ($articleFiles as $articleFile) {
                    if (Storage::exists($articleFile->file_path)) {
                        Storage::delete($articleFile->file_path);
                    }
                }

                ArticleFile::where('article_id', $article->id)->forceDelete();
            } else if (count($exsistingFiles) > 0 && count($exsistingFilesReq) > 0) {
                $articleFiles = ArticleFile::where('article_id', $article->id)
                    ->whereNotIn('id', $exsistingFilesReq)
                    ->get();

                foreach ($articleFiles as $articleFile) {
                    if (Storage::exists($articleFile->file_path)) {
                        Storage::delete($articleFile->file_path);
                    }
                }

                ArticleFile::where('article_id', $article->id)
                    ->whereNotIn('id', $exsistingFilesReq)
                    ->forceDelete();
            }

            if (is_array($request->article_files)) {
                foreach ($request->article_files as $key => $file) {
                    if ($file['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $uploadedFile = $file['file'];

                        // Generate the filename
                        $filename = 'file-' . $slug . '.' . $uploadedFile->getClientOriginalExtension();

                        // Store the file and get the storage path
                        $uploadedFile->storeAs('uploads/articles/' . $this->articleFor, $filename, 'public');

                        // Save file info in the database
                        ArticleFile::create([
                            'article_id' => $article->id, // Replace with actual article ID
                            'file_name' => $filename,
                            'file_path' => 'uploads/articles/' . $this->articleFor . '/' . $filename,
                            'type' => $file['type']
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error updating article: ' . $e->getMessage())->withInput();
        }
        return redirect()->route('articles.index', $edition->id)->with('message', 'Article updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($editionId, string $id)
    {
        $edition = self::checkEdition($editionId);
        if (empty($edition)) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $article = Article::where('id', $id)->first();
        if (empty($article)) {
            return redirect()->back()->with('message', 'Article not found');
        }

        DB::beginTransaction();
        try {
            // article keywords
            ArticleKeyword::where('article_id', $article->id)->forceDelete();

            // article references
            ArticleReference::where('article_id', $article->id)->forceDelete();

            // article contributors
            ArticleContributors::where('article_id', $article->id)->forceDelete();

            // article files
            $articleFiles = ArticleFile::where('article_id', $article->id)->get();
            if (count($articleFiles) > 0) {
                foreach ($articleFiles as $file) {
                    if (Storage::exists($file->file_path)) {
                        Storage::delete($file->file_path);
                    }

                    $file->forceDelete();
                }
            }

            // article
            $article->forceDelete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error deleting article: ' . $e->getMessage());
        }

        return redirect()->route('articles.index', $edition->id)->with('message', 'Article deleted successfully');
    }

    private function checkEdition($editionId)
    {
        $edition = Edition::where('id', $editionId)
            ->where('edition_for', $this->articleFor)
            ->first();

        if (empty($edition)) {
            return null;
        }

        return $edition;
    }

    public function sendComment(Request $request, $articleId)
    {
        $request->validate([
            'comment' => 'required|min:1',
            'attachments.*' => 'file|max:10240', // 10MB max file size
        ]);

        $article = Article::where('id', $articleId)
            ->where('article_for', $this->articleFor)
            ->first();

        if (empty($article)) {
            return redirect()->back()->with('message', 'Article not found');
        }

        DB::beginTransaction();
        try {
            $comment = ArticleComment::create([
                'article_id' => $article->id,
                'comments' => $request->comment,
                'user_id' => Auth::user()->id
            ]);

            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->storeAs('uploads/comment_attachments/' . $article->article_for, $file->getClientOriginalName(), 'public');
                    $attachment = ArticleCommentAttachment::create([
                        'article_comment_id' => $comment->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                    ]);

                    $attachments[] = $attachment->file_name;
                }
            }

            // Send notification to author
            $author = $article->user;
            if ($author) {
                $author->notify(new NewCommentNotification($article, $comment, $attachments, $this->articleFor));
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully send comment',
                'comment' => $request->comment,
                'commented_at' => date('d M Y H:i:s'),
                'attachments' => $attachments
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error send comment', 'id' => $article->id], 500);
        }
    }
}
