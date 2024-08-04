<?php

namespace App\Http\Controllers\Api\Admin\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleKeyword;
use App\Models\ArticleReference;
use App\Models\Edition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ArticleController extends Controller
{
    public function index($slug, $articleSlug)
    {
        $article = Article::with(['keywords', 'references', 'edition' => function ($q) use ($slug) {
            $q->where('slug', $slug);
        }])->where('slug', $articleSlug)->first();

        if (empty($article))
            return recordNotFoundResponse('Artikel tidak di temukan');
        return successResponse($article);
    }

    public function store(Request $request, $slug)
    {
        $edition = Edition::select('id')->where('slug', $slug)->first();

        if (empty($edition))
            return recordNotFoundResponse('Edisi tidak ditemukan');

        $rules = [
            "file" => "required||mimetypes:application/pdf|max:10000",
            "article_title" => "required",
            "author" => "required",
            "affilation" => "required",
            "country" => "required",
            "keywords" => "required",
            "abstract" => "required",
            "references" => "required",
        ];

        $validateData = Validator::make($request->all(), $rules);


        if ($validateData->fails())
            return badRequestResponse($validateData->errors()->first());

        $article_slug = str_replace(' ', '-', $request->article_title);
        $article_slug = Str::lower($article_slug);

        try {
            if ($request->file('file')) {
                $path = $request->file('file')->store('public/edition/' . $slug . '/article/' . $article_slug . '/pdf');
                $url = Storage::url($path);
            }
        } catch (\Throwable $th) {
            return internalErrorResponse("Failed upload article");
        }

        $article = new Article();
        $references = new ArticleReference();
        try {
            DB::beginTransaction();
            $article->edition_id = $edition->id;
            $article->article_title = $request->article_title;
            $article->author = $request->author;
            $article->affilation = $request->affilation;
            $article->country =  Str::lower($request->country);
            $article->abstract = $request->abstract;
            $article->path = $url;
            $article->slug = $article_slug;
            $article->save();

            foreach ($request->keywords as $keyword) {
                $keywords = new ArticleKeyword();
                $keywords->article_id = $article->id;
                $keywords->keyword = $keyword;
                $keywords->save();
            }

            foreach ($request->references as $reference) {
                $references = new ArticleReference();
                $references->article_id = $article->id;
                $references->reference = $reference;
                $references->save();
            }
            DB::commit();
            return successResponse(null, 'Success create new article');
        } catch (\Throwable $th) {
            DB::rollback();
            return internalErrorResponse($th->getMessage());
        }
    }

    public function update(Request $request, $slug, $articleSlug)
    {
        $rules = [
            "article_title" => "required",
            "author" => "required",
            "affilation" => "required",
            "country" => "required",
            "keywords" => "required",
            "abstract" => "required",
            "references" => "required",
        ];

        if ($request->file('file'))
            $rules['file'] = "mimetypes:application/pdf|max:10000";


        $validateData = Validator::make($request->all(), $rules);

        if ($validateData->fails())
            return badRequestResponse($validateData->errors()->first());

        $edition = Edition::select('id')->where('slug', $slug)->first();

        if (empty($edition))
            return recordNotFoundResponse('Edisi tidak ditemukan');


        $article = Article::where('edition_id', $edition->id)->where('slug', $articleSlug)->first();
        if (empty($article))
            return recordNotFoundResponse('Artikel tidak ditemukan');

        $article_slug = str_replace(' ', '-', $request->article_title);
        $article_slug = Str::lower($article_slug);

        try {
            if ($request->file('file')) {
                $path = $request->file('file')->store('public/edition/' . $slug . '/article/' . $article_slug . '/pdf');
                $url = Storage::url($path);
                Storage::delete($article->path);
                $article->path = $url;
            }
        } catch (\Throwable $th) {
            return internalErrorResponse("Failed upload article");
        }

        try {
            DB::beginTransaction();
            $article->edition_id = $edition->id;
            $article->article_title = $request->article_title;
            $article->author = $request->author;
            $article->affilation = $request->affilation;
            $article->country =  Str::lower($request->country);
            $article->abstract = $request->abstract;
            $article->slug = $article_slug;
            $article->save();


            ArticleKeyword::where('article_id', $article->id)
                ->whereNotIn('keyword', $request->keywords)
                ->delete();
            foreach ($request->keywords as $keyword) {

                $check_keyword = ArticleKeyword::where('article_id', $article->id)
                    ->where('keyword', $keyword)
                    ->first();
                if (empty($check_keyword)) {
                    $keywords = new ArticleKeyword();
                    $keywords->article_id = $article->id;
                    $keywords->keyword = $keyword;
                    $keywords->save();
                }
            }

            ArticleReference::where('article_id', $article->id)
                ->whereNotIn('reference', $request->references)
                ->delete();

            foreach ($request->references as $reference) {
                $check_reference = ArticleReference::where('article_id', $article->id)
                    ->where('reference', $reference)
                    ->first();
                if (empty($check_reference)) {
                    $references = new ArticleReference();
                    $references->article_id = $article->id;
                    $references->reference = $reference;
                    $references->save();
                }
            }
            DB::commit();
            return successResponse(null, 'Success create new article');
        } catch (\Throwable $th) {
            DB::rollback();
            return internalErrorResponse($th->getMessage());
        }
    }

    public function delete($slug, $articleSlug)
    {
        $edition = Edition::select('id')->where('slug', $slug)->first();

        if (empty($edition))
            return recordNotFoundResponse('Edisi tidak ditemukan');


        $article = Article::where('edition_id', $edition->id)->where('slug', $articleSlug)->first();
        if (empty($article))
            return recordNotFoundResponse('Artikel tidak ditemukan');

        try {
            DB::beginTransaction();
            Storage::delete($article->path);
            ArticleKeyword::where('article_id', $article->id)->delete();
            ArticleReference::where('article_id', $article->id)->delete();
            $article->delete();
            DB::commit();
            return successResponse(null, 'Success delete article');
        } catch (\Throwable $th) {
            DB::rollBack();
            return internalErrorResponse($th->getMessage());
        }
    }

    public function getAbstract(Request $request)
    {
        $rules = ["file" => "required||mimetypes:application/pdf|max:10000"];

        $validateData = Validator::make($request->all(), $rules, [
            'file.required' => 'You have to choose the file!',
        ]);

        if ($validateData->fails())
            return badRequestResponse($validateData->errors()->first());

        if ($request->file('file')) {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($request->file('file'));

            // Extract text from PDF
            $textContent = $pdf->getText();
            $textContent = str_replace("\t", " ", $textContent);
            $textContent = str_replace("\n", " ", $textContent);
            // $keywords[0] = $this->string_between_two_string($textContent, 'Keywords:', 'Abstract:');
            // $keywords[1] = $this->string_between_two_string($textContent, 'Kata kunci', 'Pendahuluan');

            $abstract[0] = $this->string_between_two_string($textContent, 'Abstract: ', 'Keywords');
            $abstract[1] = $this->string_between_two_string($textContent, 'Abstrak: ', 'Kata kunci');

            // $abstract = implode(" ", $abstract);

        }

        return successResponse($abstract, 'Successfully auto generated abstract');
    }

    function string_between_two_string($str, $starting_word, $ending_word)
    {
        $subtring_start = strpos($str, $starting_word);
        //Adding the starting index of the starting word to
        //its length would give its ending index
        $subtring_start += strlen($starting_word);
        //Length of our required sub string
        $size = strpos($str, $ending_word, $subtring_start) - $subtring_start;
        // Return the substring from the index substring_start of length size
        return substr($str, $subtring_start, $size);
    }
}
