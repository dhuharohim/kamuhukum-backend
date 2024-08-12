<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleContributors;
use App\Models\ArticleFile;
use App\Models\ArticleKeyword;
use App\Models\ArticleReference;
use App\Models\Edition;
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

        return view('Contents.articles.create')->with(['edition' => $edition]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($editionId, Request $request)
    {
        $edition = self::checkEdition($editionId);
        if (!$edition) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title . '-' . $this->articleFor);
        }

        $publishedDate = null;
        if ($request->status == 'production') {
            $publishedDate = date('Y-m-d');
        }

        // dd($request->files);

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
            if (count($request->references) > 0) {
                foreach ($request->references as $reference) {
                    ArticleReference::create([
                        'article_id' => $article->id,
                        'reference' => $reference
                    ]);
                }
            }

            // article contributors
            if (count($request->given_name) > 0) {
                foreach ($request->given_name as $key => $given_name) {
                    ArticleContributors::create([
                        'article_id' => $article->id,
                        'contributor_role' => $request->role[$key],
                        'given_name' => $given_name,
                        'family_name' => $request->family_name[$key],
                        'contact' => $request->contact[$key],
                        'preferred_name' => $request->preferred_name[$key],
                        'affilation' => $request->affilation[$key],
                        'country' => $request->country[$key],
                        // 'img_url',
                        'homepage_url' => $request->homepage_url[$key],
                        'orcid_id' => $request->orcid_id[$key],
                        // 'mailing_address' => $request->,
                        'bio_statement' => $request->bio_statement[$key],
                        // 'reviewing_interest',
                        'principal_contact' => $request->principal_contact[$key] == 'on' ? '1' : '0',
                        'in_browse_list' => $request->in_browse_list[$key] == 'on' ? '1' : '0'
                    ]);
                }
            }

            // article files
            if ($request->hasFile('files')) {
                $files = $request->file('files'); // Get the array of files
                $fileTypes = $request->input('file_type', []); // Get the array of file types

                foreach ($files as $index => $file) {
                    $filename = 'file-' . $slug . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/articles/' . $this->articleFor, $filename, 'public');

                    // Save file info in the database
                    ArticleFile::create([
                        'article_id' => $article->id, // Replace with actual article ID
                        'file_name' => $filename,
                        'file_path' => $path,
                        'type' => $request->file_type[$index]
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error saving article: ' . $e->getMessage());
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
        $edition = self::checkEdition($editionId);
        if (empty($edition)) {
            return redirect()->back()->with('message', 'Edition not found');
        }

        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

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
            ArticleKeyword::where('article_id', $article->id)->forceDelete();
            $keywordsArray = explode(',', $request->keywords);
            if (is_array($keywordsArray) && count($keywordsArray) > 0) {
                foreach ($keywordsArray as $keyword) {
                    ArticleKeyword::create([
                        'article_id' => $article->id,
                        'keyword' => $keyword
                    ]);
                }
            }

            // article references
            ArticleReference::where('article_id', $article->id)->forceDelete();
            if (is_array($request->references) && count($request->references) > 0) {
                foreach ($request->references as $reference) {
                    ArticleReference::create([
                        'article_id' => $article->id,
                        'reference' => $reference
                    ]);
                }
            }

            // article contributors
            ArticleContributors::where('article_id', $article->id)->forceDelete();
            if (is_array($request->given_name) && count($request->given_name) > 0) {
                foreach ($request->given_name as $key => $given_name) {
                    ArticleContributors::create([
                        'article_id' => $article->id,
                        'contributor_role' => $request->role[$key],
                        'given_name' => $given_name,
                        'family_name' => $request->family_name[$key],
                        'contact' => $request->contact[$key],
                        'preferred_name' => $request->preferred_name[$key],
                        'affilation' => $request->affilation[$key],
                        'country' => $request->country[$key],
                        // 'img_url',
                        'homepage_url' => $request->homepage_url[$key],
                        'orcid_id' => $request->orcid_id[$key],
                        // 'mailing_address' => $request->,
                        'bio_statement' => $request->bio_statement[$key],
                        // 'reviewing_interest',
                        'principal_contact' => $request->principal_contact[$key] == 'on' ? '1' : '0',
                        'in_browse_list' => $request->in_browse_list[$key] == 'on' ? '1' : '0'
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

            if ($request->hasFile('files')) {
                $files = $request->file('files'); // Get the array of files
                $fileTypes = $request->input('file_type', []); // Get the array of file types

                foreach ($files as $index => $file) {
                    $filename = 'file-' . $slug . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/articles/' . $this->articleFor, $filename, 'public');

                    // Save file info in the database
                    ArticleFile::create([
                        'article_id' => $article->id, // Replace with actual article ID
                        'file_name' => $filename,
                        'file_path' => $path,
                        'type' => $request->file_type[$index]
                    ]);
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
            ArticleKeyword::where('article_id', $article->id)->delete();

            // article references
            ArticleReference::where('article_id', $article->id)->delete();

            // article contributors
            ArticleContributors::where('article_id', $article->id)->delete();

            // article files
            $articleFiles = ArticleFile::where('article_id', $article->id)->get();
            if (count($articleFiles) > 0) {
                foreach ($articleFiles as $file) {
                    if (Storage::exists($file->file_path)) {
                        Storage::delete($file->file_path);
                    }

                    $file->delete();
                }
            }

            // article
            $article->delete();

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
}