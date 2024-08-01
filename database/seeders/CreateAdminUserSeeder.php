<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $user = User::create([
            'name' => 'super_admin', 
            'email' => 'super_admin@admin.com',
            'password' => bcrypt('password')
        ]);
    
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'api']);     
     
        $user->assignRole($role);
    }
}
