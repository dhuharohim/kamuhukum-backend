<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\ProfileAuthor;
use App\Models\ProfileUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Profiler\Profile;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login_from' => 'required|string|in:law,economic'
        ]);

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user()->load('profile');
            $token = $user->createToken($request->login_from)->plainTextToken;

            return response()->json([
                'token' => $token,
                'message' => 'Successfully logged in',
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized bro'
        ], 401);
    }

    public function register(RegisterRequest $request)
    {
        $roleName = $request->user_type === 'law' ? 'author_law' : 'author_economic';
        $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();
        if ($role) {
        } else {
            return response()->json(['message' => 'Role not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create([
                'username' => $request->username,
                'user_type' => $request->user_type,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($role);

            // Create the profile user
            $profile = ProfileAuthor::create([
                'user_id' => $user->id,
                'given_name' => $request->given_name,
                'family_name' => $request->family_name,
                // 'phone' => $request->phone,
                // 'preferred_name' => $request->preferred_name,
                'affilation' => $request->affilation,
                'country' => $request->country,
                // 'img_url' => $request->img_url,
                // 'homepage_url' => $request->homepage_url,
                // 'orchid_id' => $request->orchid_id,
                // 'mailing_address' => $request->mailing_address,
                // 'bio_statement' => $request->bio_statement,
            ]);

            if (isset($request->reviewing_interest)) {
                $profile->reviewing_interest = $request->reviewing_interest;
                $profile->save();
            }

            DB::commit();
            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function userData()
    {
        $user = Auth::user()->load('profile');
        if (empty($user)) {
            return unauthorizedResponse("Sorry, you are not authorized to use this feature");
        }
        return response()->json($user);
    }

    public function logout(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        if ($user) {
            // Revoke all tokens
            $user->tokens()->delete();
        }

        // Return a success response
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function editProfile($from, $type, Request $request)
    {
        if (!in_array($from, ['law', 'economic'])) {
            return badRequestResponse();
        }

        $user = Auth::user();
        $profile = ProfileAuthor::where('user_id', $user->id)->first();
        if (empty($profile)) {
            return recordNotFoundResponse('Profile not found');
        }

        DB::beginTransaction();
        try {
            switch ($type):
                case 'identity':
                    $request->validate([
                        'givenName' => 'required',
                        'familyName' => 'required'
                    ]);

                    $profile->update([
                        'given_name' => request('givenName'),
                        'family_name' => request('familyName'),
                        'preferred_name' => request('preferredName') ?? '',
                    ]);
                    break;
                case 'contact':
                    $request->validate([
                        'email' => 'required',
                        'affilation' => 'required',
                        'country' => 'required'
                    ]);

                    $profile->update([
                        'affilation' => request('affilation'),
                        'country' => request('country'),
                        'phone' => request('phone') ?? '',
                        'mailing_address' => request('mailingAddress') ?? '',
                    ]);

                    $user->email = request('email');
                    $user->save();
                    break;
                case 'public':
                    $path = null;
                    if ($request->hasFile('img_url')) {
                        $path = $request->file('img_url')->store('public/profile_pictures/' . $user->username);
                    }

                    $profile->update([
                        'bio_statement' => request('bioStatement'),
                        'orcid_id' => request('orcidId'),
                        'homepage_url' => request('homepageUrl'),
                        'img_url' => $path,
                    ]);
                    break;
                case 'password':
                    $request->validate([
                        'currPass' => 'required',
                        'newPass' => 'required|min:8|confirmed',
                        'confirmed' => 'required'
                    ]);

                    if (!Hash::check(request('currPass'), $user->password)) {
                        return badRequestResponse('Current password is incorrect');
                    }

                    $user->password = Hash::make(request('newPass'));
                    $user->save();
                    break;
                default:
                    break;
            endswitch;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return internalErrorResponse($e->getMessage());
        }

        return successResponse(null, 'Profile updated successfully');
    }
}
