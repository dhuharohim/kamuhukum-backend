<?php

namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::select('id', 'name')->get();
        return successResponse($categories);
    }

    public function store(Request $request) {

        $rules = [
            'name' => 'required'
        ];
         $validateData = Validator::make($request->all(), $rules);  

        if($validateData->fails()){

           return badRequestResponse($validateData->errors());
        }

        Category::create([
            "name" => $request->name
        ]);

        successResponse('Success created new category');
    }
}
