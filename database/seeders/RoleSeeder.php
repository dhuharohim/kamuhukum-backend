<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin_law', 'guard_name' => 'web'],
            ['name' => 'editor_law', 'guard_name' => 'web'],
            ['name' => 'author_law', 'guard_name' => 'api'],
            ['name' => 'admin_economy', 'guard_name' => 'web'],
            ['name' => 'editor_economy', 'guard_name' => 'web'],
            ['name' => 'author_economy', 'guard_name' => 'api'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
