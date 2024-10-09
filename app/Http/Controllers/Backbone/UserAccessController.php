<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Models\ProfileAuthor;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserAccessController extends Controller
{
    private $userFor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->userFor = $user->hasRole(['admin_law']) ? 'law' : 'economy';
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = ['editor_' . $this->userFor, 'author_' . $this->userFor];
        $users = User::where('id', '!=', Auth::user()->id)->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->with('roles')->get();

        return view('Contents.users.list')->with([
            'users' => $users,
            'for' => $this->userFor,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = ['editor_' . $this->userFor, 'author_' . $this->userFor];
        return view('Contents.users.create')->with(['roles' => $roles, 'for' => $this->userFor]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required',
        ]);

        $role = Role::where('name', $request->role)->first();
        if (!$role) {
            return back()->with('error', 'Role not found');
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($role);
            if (in_array($request->role, ['author_law', 'author_economy'])) {
                $request->validate([
                    'given_name' => 'required',
                    'family_name' => 'required',
                    'affilation' => 'required',
                    'country' => 'required',
                ]);

                ProfileAuthor::create([
                    'user_id' => $user->id,
                    'author_type' => $request->role == 'author_law' ? 'law' : 'economy',
                    'given_name' => $request->given_name,
                    'family_name' => $request->family_name,
                    'phone' => $request->phone,
                    'email' => $user->email,
                    'preferred_name' => $request->preferred_name,
                    'affilation' => $request->affilation,
                    'country' => $request->country,
                    // 'img_url',
                    'homepage_url' => $request->homepage_url,
                    'orcid_id' => $request->orcid_id,
                    // 'mailing_address' ,
                    'bio_statement' => $request->bio_statement,
                    // 'reviewing_interest' => $request
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', $e->getMessage())->withInput();
        }

        return redirect()->route('users-access.index')->with('message', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roles = ['editor_' . $this->userFor, 'author_' . $this->userFor];
        $user = User::where('id', $id)->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->with('profile')->first();

        if (empty($user)) {
            return redirect()->route('users-access.index')->with('message', 'User not found');
        }

        return view('Contents.users.show')->with(['roles' => $roles, 'user' => $user, 'for' => $this->userFor]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            return redirect()->route('users-access.index')->with('message', 'User not found');
        }

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|confirmed',
            'role' => 'required',
        ]);

        $role = Role::where('name', $request->role)->first();
        if (!$role) {
            return back()->with('error', 'Role not found');
        }

        DB::beginTransaction();
        try {
            // Update user details
            $user->update([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            // Sync the role
            $user->syncRoles($role);

            if (in_array($request->role, ['author_law', 'author_economy'])) {
                $request->validate([
                    'given_name' => 'required',
                    'family_name' => 'required',
                    'affilation' => 'required',
                    'country' => 'required',
                ]);

                $profileAuthor = ProfileAuthor::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'author_type' => $request->role == 'author_law' ? 'law' : 'economy',
                        'given_name' => $request->given_name,
                        'family_name' => $request->family_name,
                        'phone' => $request->phone,
                        'email' => $user->email,
                        'preferred_name' => $request->preferred_name,
                        'affilation' => $request->affilation,
                        'country' => $request->country,
                        // 'img_url',
                        'homepage_url' => $request->homepage_url,
                        'orcid_id' => $request->orcid_id,
                        // 'mailing_address' ,
                        'bio_statement' => $request->bio_statement,
                        // 'reviewing_interest' => $request
                    ]
                );
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', $e->getMessage())->withInput();
        }

        return redirect()->route('users-access.index')->with('message', 'User updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
