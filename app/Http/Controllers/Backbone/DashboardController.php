<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('Contents.dashboard')->with([
            'page' => 'dashboard',
            'user' => $user,
            'journal' => $user->hasRole(['admin_law', 'editor_law']) ? 'kamuhukumjournal.com' : 'oeajournal.com'
        ]);
    }
}
