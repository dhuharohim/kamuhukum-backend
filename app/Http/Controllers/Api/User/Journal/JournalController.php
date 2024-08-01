<?php

namespace App\Http\Controllers\Api\User\Journal;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Edition;
use Illuminate\Http\Request;

class JournalController extends Controller
{

    public function currentPage() {
        $currentEdition = Edition::where('status', 'Published')
                                ->with('articles')
                                ->whereNull('deleted_at')
                                ->first();

                                
        if(empty($currentEdition)) {
            return recordNotFoundResponse('Tidak menemukan edisi terbaru.');
        }

        return successResponse($currentEdition);
    }

    public function archievedPage() {
        $archievedEditions = Edition::where('status', 'Archive')
                                    ->with('articles')
                                    ->whereNull('deleted_at')
                                    ->orderBy('publish_date', 'DESC')
                                    ->get();

        if(count($archievedEditions) == 0) {
            return recordNotFoundResponse('Tidak menemukan edisi yang terarsip');
        }

        return successResponse($archievedEditions);
    }

    public function showEdition($slug) {
        $archievedEditions = Edition::where('slug', $slug)
                                    ->where('status', 'Archive')
                                    ->with('articles')
                                    ->whereNull('deleted_at')
                                    ->orderBy('publish_date', 'DESC')
                                    ->first();

        if(empty($archievedEditions)) {
            return recordNotFoundResponse('Tidak menemukan edisi yang terarsip');
        }

        return successResponse($archievedEditions);
    }

    public function showArticle($slug) {
        $article = Article::where('slug', $slug)->with(['edition', 'keywords', 'references'])->first();

        if(empty($article)) {
            return recordNotFoundResponse('Artikel tidak ditemukan');
        }

        $article->viewedIncrease();
        return successResponse($article);
    }

    public function searchArticle(Request $request) {
        $articles = Article::query();
    
        if (!empty($request->nameArticle)) {
            $articles->where('article_title', 'LIKE', '%' . $request->nameArticle . '%');
        }

        if(!empty($request->nameAuthor)) {
            $articles->where('author', 'LIKE', '%' . $request->nameAuthor . '%');
        }
    
        if (!empty($request->publishedAfter) && !empty($request->publishedBefore)) {
            $articles->whereBetween('created_at', [$request->publishedAfter, $request->publishedBefore]);
        } else {
            if (!empty($request->publishedAfter)) {
                $articles->where('created_at', '>', $request->publishedAfter);
            }
    
            if (!empty($request->publishedBefore)) {
                $articles->where('created_at', '<', $request->publishedBefore);
            }
        }
    
        $result = $articles->get();
        
        return successResponse($result);
    }
}
