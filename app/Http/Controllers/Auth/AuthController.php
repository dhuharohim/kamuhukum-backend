<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;
use DB;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function index()
    {
        $userId = auth('sanctum')->user()->id;
        $user = User::with('roles')->where('id', $userId)->first();
        if (empty($user))
            return unauthorizedResponse("Sorry, you are not authorized to use this feature");

        return successResponse(new UserResource($user));
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return successResponse(null, 'Successfully logout');
        } catch (\Throwable $th) {
            return internalErrorResponse('Failed to logout, please try again later');
        }
    }

    /**
     * Register a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'affilation' => 'required',
                'country' => 'required'
            ],
            [
                'email.required' => 'Email field is required',
                'email.unique' => 'Email has been taken.',
                'username.required' => 'Username field is required',
                'username.unique' => 'Username has been taken.',
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'affilation.required' => 'affilation is required',
                'country.required' => 'Country is required'
            ]
        );

        try {
            DB::transaction(function () use ($request) {
                $user = new User();
                $user->create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                ]);

                $role = Role::where('name', 'user')->first();
                $user->assignRole($role);
            });
        } catch (\Exception $e) {
            return badCredentialsResponse($e);
        }

        $userId = User::where('username', $request->username)
            ->where('email', $request->email)
            ->first()->id;

        if (empty($userId)) {
            return badCredentialsResponse('Gagal membuat akun, silahkan coba lagi atau hubungi call centers.');
        } else {
            try {
                DB::transaction(function () use ($request, $userId) {
                    $profile = new Profile();
                    $profile->create([
                        'user_id' => $userId,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'affilation' => $request->affilation,
                        'country' => $request->country,
                        'is_data_collected' => $request->is_data_collected == 'true' ? true : false,
                    ]);
                });
            } catch (\Exception $e) {
                return badCredentialsResponse($e);
            }
            return successResponse([], 'Akun berhasil dibuat');
        }
    }
}
