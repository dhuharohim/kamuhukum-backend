<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BackboneUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin_kamuhukum',
                'email' => 'admin@kamuhukumjournal.com',
                'password' => 'KamuhukumAdmin@2024!',
                'role' => 'admin_law',
            ],
            [
                'username' => 'editor_kamuhukum',
                'email' => 'editor@kamuhukumjournal.com',
                'password' => 'KamuhukumEditor@2024!',
                'role' => 'editor_law',
            ],
            [
                'username' => 'admin_oea',
                'email' => 'admin@oeajournal.com',
                'password' => 'OEA@2024!',
                'role' => 'admin_economy',
            ],
            [
                'username' => 'editor_oea',
                'email' => 'editor@oeajournal.com',
                'password' => 'OEAEditor@2024!',
                'role' => 'editor_economy',
            ],
            [
                'username' => 'admin_law',
                'email' => 'admin@law.com',
                'password' => 'password',
                'role' => 'admin_law',
            ],
            [
                'username' => 'admin_eco',
                'email' => 'admin@eco.com',
                'password' => 'password',
                'role' => 'admin_economy',
            ]
        ];

        foreach ($users as $user) {
            $userCreate = User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
            ]);

            $userCreate->assignRole($user['role']);
        }
    }
}
