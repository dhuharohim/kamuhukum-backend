<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'user', 
            'username' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('password')
        ]);
    
        $role = Role::create(['name' => 'user', 'guard_name' => 'api']);
     
        $user->assignRole($role);
    }
}
