<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CmsHelper;

class ContentController extends Controller
{
    use CmsHelper;
    public function getContentSection($from, $slug)
    {
        $cms = $this->getCmsSection($from, $slug);
        return response()->json($cms, 200);
    }
}
