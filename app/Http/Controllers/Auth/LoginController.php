<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use stdClass;

class LoginController extends Controller
{
    /**
     * Logs the user into the application by issuing them an API Key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return badCredentialsResponse('Sorry, your login details are invalid!');
        }

        $token = new stdClass();

        if($user->hasRole('super_admin')) {
            $token = $user->createToken("ADMIN_PANEL_TOKEN");
        } else if($user->hasRole('admin')) {
            $token = $user->createToken("ADMIN_PANEL_TOKEN");
        } else { 
            $token = $user->createToken("USER_TOKEN");
        }
        $user->update([
            'last_login_at' => now(),
            'api_token' => $token->plainTextToken
        ]);
        $user->api_token = $token->plainTextToken;
        $user->save();
        // $permissions = $user->getAllPermissions();
        return successResponse([
            'api_token' => $token->plainTextToken,
        ], "Successfully login with ".$user->name);
    }

    // public function loginWithAppToken(Request $request): JsonResponse
    // {
    //     $this->validate($request, [
    //         'token' => 'required',
    //     ]);

    //     $appLoginToken = AppLoginToken::where('token', $request->token)->where('expired', 0)->first();
    //     if (empty($appLoginToken)) {
    //         return badCredentialsResponse('Invalid login request');
    //     }

    //     $user = User::where('id', $appLoginToken->user_id)->first();
    //     if (empty($user)) {
    //         return badCredentialsResponse('Sorry, your login details are invalid!');
    //     }

    //     // User was correct, issue an api token
    //     $rawToken = Str::random(191);
    //     $challengeToken = hash('sha256', $user->id . Str::random(190));
    //     DB::transaction(function () use ($rawToken, $challengeToken, $user) {
    //         ApiKey::create([
    //             'user_id' => $user->id,
    //             'api_key' => $rawToken,
    //             'challenge_token' => $challengeToken,
    //         ]);
    //     }, 5);

    //     $appLoginToken->expired = 1;
    //     $appLoginToken->save();

    //     $apiKey = $rawToken . '.' . $challengeToken;
    //     $token = $user->createToken("ADMIN_PANEL_TECHNICIAN_TOKEN");

    //     $data = [
    //         'api_key' => $token->plainTextToken,
    //         'user_level' => $user->user_level,
    //         'user' => $user,
    //     ];

    //     return dataResponse($data);
    // }
}
