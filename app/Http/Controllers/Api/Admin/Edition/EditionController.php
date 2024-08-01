<?php

namespace App\Http\Controllers\Api\Admin\Edition;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleKeyword;
use App\Models\ArticleReference;
use App\Models\Edition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class EditionController extends Controller
{
    public function index(Request $request) {
        $edition = Edition::select([ 'name_edition','volume','issue','description','publish_date','status','slug','year','img']);

        if(!empty($request->search)){
            $request->search;
            $edition = $edition->where('name_edition', 'like', '%'.$request->search.'%');
        }

        if(!empty($request->year)){
            $edition = $edition->where('year', $request->year);
        }

        if(!empty($request->status)) {
            $edition = $edition->where('status', $request->status);
        }

        $edition = $edition->get();

        return successResponse($edition);
    }

    public function store (Request $request) {
        $rules = [ 
            "img" => "mimes:jpg,png,jpeg,gif,svg|max:2048",
            'edition_name' => 'required',
            'volume' => 'required',
            'issue' => 'required',
            'description' => 'required',
            'year' => 'required'
        ];

         $validateData = Validator::make($request->all(), $rules);  

        if($validateData->fails())
            return badRequestResponse($validateData->errors()->first());
        

        $slug = str_replace(' ', '-', $request->edition_name);
        $slug = Str::lower($slug);

       

        $edition = new Edition();
        $edition->name_edition = $request->edition_name;
        $edition->volume = $request->volume;
        $edition->issue =  $request->issue;
        $edition->description = $request->description;  
        $edition->publish_date = $request->status  == 'publish' ? Carbon::now()->format('Y-m-d') : null;       
        $edition->status = $request->status == 'publish' ? 'Published' : 'Draft';
        $edition->slug = $slug;

         if($request->file('img')) {
           $path = $request->file('img')->store('public/edition/'.$slug."/image");
            $url = Storage::url($path);
            $edition->img = $url;
        }

        $edition->year= Carbon::parse($request->year)->format('Y');
        $edition->save();

        if($request->status == 'publish'){
            Edition::where('id', '!=', $edition->id)
                    ->where('status', 'Published')
                    ->update([
                        'status' => 'Archive'
                    ]);
        }

        return successResponse($edition,'Success created new editions');

    }

    public function show ($slug) {
        
        $edition = Edition::with(['articles', 'articles.keywords', 'articles.references'])->where('slug',$slug)->first();
        if(empty($edition))
            return recordNotFoundResponse('Edisi tidak ditemukan');

        return successResponse($edition);
    }

    public function update(Request $request,$slug) {
        $edition = Edition::where('slug', $slug)->first();

        if(empty($edition))
            return recordNotFoundResponse('Edisi tidak ditemukan');

        $slug = str_replace(' ', '-', $request->name_edition);
        $edition->name_edition = $request->name_edition;
        $edition->volume = $request->volume;
        $edition->issue = $request->issue;
        $edition->description = $request->description;
        $edition->slug = $slug;
        $edition->year = $request->year;

         if($request->file('img')) {
           $path = $request->file('img')->store('public/edition/'.$slug."/image");
           $url = Storage::url($path);
           Storage::delete($edition->img);
           $edition->img = $url;
        }
        $edition->save();

        return successResponse($edition,'Berhasil mengubah edisi');
   }

   public function updateStatus(Request $request, $slug) {
    $rules = [
        'status' => 'required',
    ];

    $validateData = Validator::make($request->all(), $rules);  

    if($validateData->fails())
        return badRequestResponse($validateData->errors()->first());

    $edition = Edition::where('slug', $slug)->first();

    if(empty($edition))
        return recordNotFoundResponse('Edisi tidak ditemukan');

    if($request->status == 'publish'){
        $edition->status = 'Published';
        Edition::where('status', 'Published')->update([
            'status' => 'Archive'
        ]);
    }
    
    if($request->status == 'archive')
        $edition->status = 'Archive';

    $edition->save();

    return successResponse(null,'Berhasil mengubah status');
   }


   public function delete($slug) {

    $edition = Edition::where('slug',$slug)->first();
    if(empty($edition))
        return recordNotFoundResponse('Edisi tidak ditemukan');

    $article = Article::where('edition_id', $edition->id);
    
    $articleIds = $article->get()->pluck('id')->toArray();
    $articlePath = $article->get()->pluck('path')->toArray();
    
    try {
        DB::beginTransaction();
        ArticleKeyword::whereIn('article_id', $articleIds)->delete();
        ArticleReference::whereIn('article_id', $articleIds)->delete();
        $article->delete();
        $edition->delete();
        
        if(count($articlePath) != 0)
            Storage::delete($articlePath);

        DB::commit();
        return successResponse(null, "Success delete edition");
    } catch (\Throwable $th) {
        DB::rollBack();
        return internalErrorResponse($th->getMessage());
    }
   }
}
