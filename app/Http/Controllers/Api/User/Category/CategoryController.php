<?php

namespace App\Http\Controllers\Api\User\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
     public function index(Request $request) 
    {
        $categories = Category::select('id', 'name');
        if(!empty($request->search)) 
            $categories = $categories->where('name', 'like', '%'.$request->search.'%');

        $categories = $categories->get();
        if(count($categories) == 0)
            return successResponse($categories, 'Record not found');

        return successResponse($categories);
    }
}
