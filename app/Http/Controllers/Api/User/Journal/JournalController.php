<?php

namespace App\Http\Controllers\Api\User\Journal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleFileRequest;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleContributors;
use App\Models\ArticleFile;
use App\Models\ArticleKeyword;
use App\Models\Edition;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Hamcrest\Type\IsBoolean;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class JournalController extends Controller
{
    public function currentPage($from, $cred = null)
    {
        $currentEdition = Edition::where('edition_for', $from)
            ->where('status', 'Published')
            ->whereNull('deleted_at')
            ->first();

        if (empty($currentEdition)) {
            return recordNotFoundResponse('Tidak menemukan edisi terbaru.');
        }

        $relation = ['keywords', 'authors', 'references'];
        // if ($cred) {
        //     $user = User::where('email', $cred)->first();
        //     if ($user->hasRole('author_' . $from)) {
        //         $relation[] = 'files';
        //     } else {
        //         return unauthorizedResponse();
        //     }
        // }

        $articles = Article::where('edition_id', $currentEdition->id)
            ->with($relation)
            ->where('status', 'production')
            ->where('article_for', $from)
            ->get();

        $currentEdition->articles = $articles;

        return successResponse($currentEdition);
    }

    public function archievedPage($from, $cred = null)
    {
        $archievedEditions = Edition::where('edition_for', $from)
            // ->where('status', 'Archive')
            ->with('articles')
            ->whereNull('deleted_at')
            ->orderBy('publish_date', 'DESC')
            ->get();

        if (count($archievedEditions) == 0) {
            return recordNotFoundResponse('Tidak menemukan edisi yang terarsip');
        }

        return successResponse($archievedEditions);
    }

    public function showEdition($from, $slug, $cred = null)
    {
        $archievedEditions = Edition::where('edition_for', $from)
            ->where('slug', $slug)
            // ->where('status', 'Archive')
            // ->with('articles')
            ->whereNull('deleted_at')
            ->orderBy('publish_date', 'DESC')
            ->first();

        if (empty($archievedEditions)) {
            return recordNotFoundResponse('Tidak menemukan edisi yang terarsip');
        }

        $relation = ['keywords', 'authors', 'references'];
        if ($cred) {
            $user = User::where('email', $cred)->first();
            if ($user->hasRole('author_' . $from)) {
                $relation[] = 'files';
            } else {
                return unauthorizedResponse();
            }
        }

        $articles = Article::where('edition_id', $archievedEditions->id)
            ->where('article_for', $from)
            ->with($relation)
            ->get();

        $archievedEditions->articles = $articles;
        return successResponse($archievedEditions);
    }

    public function showArticle($from, $slug, $cred = null)
    {
        $relation = ['edition', 'keywords', 'references', 'authors', 'references'];
        if ($cred) {
            $user = User::where('email', $cred)->first();
            if ($user->hasRole('author_' . $from)) {
                $relation[] = 'files';
            } else {
                return unauthorizedResponse();
            }
        }

        $article = Article::where('slug', $slug)
            ->where('article_for', $from)
            ->with($relation)
            ->first();

        if (empty($article)) {
            return recordNotFoundResponse('Artikel tidak ditemukan');
        }

        $article->viewedIncrease();
        return successResponse($article);
    }

    public function searchArticle($from, Request $request)
    {
        $articles = Article::query();
        $articles->where('article_for', $from);
        if (!empty($request->nameArticle)) {
            $articles->where('article_title', 'LIKE', '%' . $request->nameArticle . '%');
        }

        if (!empty($request->nameAuthor)) {
            $nameAuthor = $request->nameAuthor;
            $articles->whereHas('authors', function ($query) use ($nameAuthor) {
                $query->where('given_name', 'LIKE', '%' . $nameAuthor . '%')
                    ->orWhere('family_name', 'LIKE', '%' . $nameAuthor . '%');
            });
        }

        if (!empty($request->publishedAfter) && !empty($request->publishedBefore)) {
            $articles->whereBetween('published_date', [$request->publishedAfter, $request->publishedBefore]);
        } else {
            if (!empty($request->publishedAfter)) {
                $articles->where('published_date', '>', $request->publishedAfter);
            }

            if (!empty($request->publishedBefore)) {
                $articles->where('published_date', '<', $request->publishedBefore);
            }
        }

        $result = $articles->with(['authors'])->get();

        return successResponse($result);
    }

    public function submitSubmission($from, $step, Request $request)
    {
        switch ($step):
            case 'section':
                // submit section data
                return self::submitSection($request, $from);
                break;
            case 'file':
                // submit file data
                $articleFileRequest = ArticleFileRequest::createFromBase($request);
                return self::submitFile($articleFileRequest, $from);
                break;
            case 'metadata':
                // submit metadata data
                return self::submitMetadata($request);
                break;
            case 'finish':
                // submit final submission data
                return self::submitFinalSubmission($request, $from);
                break;
            default:
                return badRequestResponse('Invalid step');
        endswitch;
    }

    private function submitSection(Request $request, $from): JsonResponse
    {
        $request->validate([
            'section' => 'required|string|in:article,general_article',
        ]);

        DB::beginTransaction();
        try {
            $uuid = '';
            if (isset($request->uuid)) {
                $article = Article::where('uuid', $request->uuid)
                    ->where('user_id', Auth::user()->id)
                    ->where('article_for', $from)
                    ->update([
                        'section' => $request->section,
                        'comments_for_editor' => $request->comments_for_editor,
                    ]);

                $uuid = $request->uuid;
            } else {
                $article = Article::create([
                    'user_id' => Auth::user()->id,
                    'article_for' => $from,
                    'section' => $request->section,
                    'comments_for_editor' => $request->comments_for_editor ?? null,
                    'status' => 'incomplete'
                ]);

                $uuid = Crypt::encrypt($article->id);
                $article->update([
                    'uuid' => $uuid
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully submitted',
                'uuid' => $uuid
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    private function submitFile(ArticleFileRequest $request, $from): JsonResponse
    {
        $article = Article::where('uuid', $request->uuid)
            ->where('user_id', Auth::user()->id)
            ->where('article_for', $from)
            ->first();

        if (empty($article)) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $articleFiles = ArticleFile::where('article_id', $article->id)->get();
            if (count($articleFiles) > 0) {
                foreach ($articleFiles as $articleFile) {
                    $filePath = $articleFile->file_path;
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    }

                    $articleFile->delete();
                }
            }

            foreach ($request->article_files as $index => $fileData) {
                $path = 'failed';
                if ($request->hasFile("article_files.$index.file")) {
                    $path = $request->file("article_files.$index.file")->storeAs('uploads/articles/' . $article->article_for, $fileData['name'], 'sftp');
                    // $url = Storage::url($path);
                }

                ArticleFile::create([
                    'article_id' => $article->id,
                    'file_name' => $fileData['name'],
                    'file_path' => $path,
                    'type' => $fileData['type']
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully submitted',
                'uuid' => $article->uuid
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    private function submitMetadata(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
            'title' => 'required',
            'abstract' => 'required',
            'contributors' => 'required|array|min:1',
        ]);

        $article = Article::where('uuid', $request->uuid)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (empty($article)) {
            return recordNotFoundResponse('Artikel tidak ditemukan');
        }

        $articleSlug = str_replace(' ', '-', $request->title);
        $articleSlug = Str::lower($articleSlug) . '-' . date('Ymdhis', strtotime($article->created_at));

        DB::beginTransaction();
        try {
            $article->update([
                'prefix' => $request->prefix ?? null,
                'subtitle' => $request->subtitle ?? null,
                'title' => $request->title,
                'abstract' => $request->abstract,
                'slug' => $articleSlug
            ]);

            ArticleContributors::where('article_id', $article->id)->delete();
            foreach ($request->contributors as $contributor) {
                ArticleContributors::create([
                    'article_id' => $article->id,
                    'contributor_role' => $contributor['role'] ?? 'Author',
                    'given_name' => $contributor['given_name'] ?? null,
                    'family_name' => $contributor['family_name'] ?? null,
                    'contact' => $contributor['contact'] ?? null,
                    // 'phone' => $contributorphone,
                    'preferred_name' => $contributor['preferred_name'] ?? null,
                    'affilation' => $contributor['affilation'] ?? null,
                    'country' => $contributor['country'] ?? null,
                    // 'img_url' => $contributorimg_url,
                    'homepage_url' => $contributor['homepage_url'] ?? null,
                    'orcid_id' => $contributor['orcid_id'] ?? null,
                    // 'mailing_address' => $contributor['mailing_address'],
                    'bio_statement' => $contributor['bio_statement'] ?? null,
                    // 'reviewing_interest',
                    'principal_contact' => $contributor['principal_contact'] == true ? 1 : 0,
                    'in_browse_list' => $contributor['in_browse_list'] == true ? 1 : 0
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully submitted',
                'uuid' => $article->uuid
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    private function submitFinalSubmission(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
        ]);

        $article = Article::where('uuid', $request->uuid)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (empty($article)) {
            return recordNotFoundResponse('Artikel tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            $article->update([
                'status' => 'submission'
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Successfully submitted',
                'uuid' => $article->uuid
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function getUserArticles($from)
    {
        $articles = Article::where('user_id', Auth::user()->id)
            ->where('article_for', $from)
            ->get();

        return response(['message' => 'Success', 'data' => $articles], 200);
    }

    public function getUserArticle($from, $uuidArticle)
    {
        $articles = Article::where('uuid', $uuidArticle)
            ->where('user_id', Auth::user()->id)
            ->where('article_for', $from)
            ->with(['authors', 'files'])
            ->first();

        return response(['message' => 'Success', 'data' => $articles], 200);
    }

    public function deleteSubmission($from, $uuid)
    {
        $article = Article::where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->where('article_for', $from)
            ->first();

        if (empty($article)) {
            return recordNotFoundResponse('Artikel tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            $articleFiles = ArticleFile::where('article_id', $article->id)->get();
            if (count($articleFiles) > 0) {
                foreach ($articleFiles as $articleFile) {
                    $filePath = $articleFile->file_path;
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    }

                    $articleFile->delete();
                }
            }

            ArticleKeyword::where('article_id', $article->id)->delete();
            ArticleContributors::where('article_id', $article->id)->delete();

            $article->delete();
            DB::commit();
            return response()->json(['message' => 'Successfully deleted'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function getHomeData($from)
    {
        if (!$from) {
            return response(['message' => 'Invalid request'], 400);
        }

        $currentEdition = Edition::where('edition_for', $from)
            ->where('status', 'Published')
            ->whereNull('deleted_at')
            ->first();

        if (!empty($currentEdition)) {
            $artciles = Article::where('edition_id', $currentEdition->id)
                ->with(['authors'])
                ->where('article_for', $from)
                ->where('status', 'production')
                ->get();

            $currentEdition->articles = $artciles;
        }

        $announcements = Announcement::with('edition')
            ->where('announcement_for', $from)
            ->orderBy('created_at', 'DESC')
            ->take(4)
            ->get();

        return response()->json(['current' => $currentEdition, 'announcements' => $announcements]);
    }
}
