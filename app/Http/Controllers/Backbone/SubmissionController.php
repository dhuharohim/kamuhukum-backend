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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SubmissionController extends Controller
{
    private $editionFor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->editionFor = $user->hasRole(['admin_law', 'editor_law']) ? 'law' : 'economic';
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::where('article_for', $this->editionFor)
            ->where('status', 'submission', 'incomplete')
            ->with('edition')
            ->get();

        return view('Contents.submission.list')->with('articles', $articles);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::where('id', $id)->with(['keywords', 'references', 'edition', 'authors', 'files', 'user', 'comments.user'])->first();
        if (!$article) {
            return redirect()->back()->with('message', 'Article not found');
        }

        $editions = Edition::where('edition_for', $this->editionFor)->get();

        return view('Contents.submission.show')->with(['article' => $article,  'editions' => $editions]);
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
    public function update(Request $request, string $id)
    {
        $editionId = null;
        if (isset($request->edition)) {
            $edition = Edition::where('id', $request->edition)
                ->where('edition_for', $this->editionFor)
                ->first();

            if (empty($edition)) {
                return redirect()->back()->with('message', 'Edition not found');
            }

            $editionId = $edition->id;
        }

        $article = Article::where('id', $id)
            ->where('article_for', $this->editionFor)
            ->first();

        if (empty($article)) {
            return redirect()->back()->with('message', 'Article not found');
        }

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title . '-' . $this->editionFor);
        }

        $publishedDate = null;
        if ($request->status == 'production') {
            $publishedDate = date('Y-m-d');
        }

        DB::beginTransaction();
        try {
            $article->update([
                'prefix' => $request->prefix,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'section' => $request->section,
                'status' => $request->status,
                'abstract' => $request->abstract,
                'slug' => $slug,
                'published_date' => $publishedDate,
                'edition_id' => $editionId
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
                        'contact' => $contributor['contact'],
                        'preferred_name' => $contributor['preferred_name'],
                        'affilation' => $contributor['affilation'],
                        'country' => $contributor['country'],
                        // 'img_url',
                        'homepage_url' => $contributor['homepage_url'],
                        'orcid_id' => $contributor['orcid_id'],
                        // 'mailing_address' => $contributor->,
                        'bio_statement' => $contributor['bio_statement'],
                        // 'reviewing_interest',
                        'principal_contact' => $contributor['principal_contact'] == 'on' ? '1' : '0',
                        'in_browse_list' => $contributor['in_browse_list'] == 'on' ? '1' : '0'
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
                        $uploadedFile->storeAs('uploads/articles/' . $this->editionFor, $filename, 'public');

                        // Save file info in the database
                        ArticleFile::create([
                            'article_id' => $article->id, // Replace with actual article ID
                            'file_name' => $filename,
                            'file_path' => 'uploads/articles/' . $this->editionFor . '/' . $filename,
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
        return redirect()->route('submissions.index')->with('message', 'Submissions updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
