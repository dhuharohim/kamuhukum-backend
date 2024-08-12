<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthBackboneController extends Controller
{
    public function loginPage()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        };

        return view('Auth.login');
    }

    public function submitLogin(Request $request)
    {
        $request->validate([
            'web_type' => 'required|string|in:law,economy',
            'email' => 'required|email',
            'password' => 'required|'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->hasRole(['admin_' . $request->web_type, 'editor_' . $request->web_type])) {
                return redirect()->route('auth-backbone.login')->with('message', 'You are not authorized to access this web')->withInput();
            }

            Auth::login($user);
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('auth-backbone.login')->with('message', 'Invalid email or password')->withInput();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth-backbone.login');
    }
}
