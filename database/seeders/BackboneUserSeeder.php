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
                'username' => 'admin_legisinsight',
                'email' => 'admin@legisinsightjournal.com',
                'password' => 'LegisInsightJournalAdmin@2024!',
                'role' => 'admin_law',
            ],
            [
                'username' => 'editor_legisinsight',
                'email' => 'editor@legisinsightjournal.com',
                'password' => 'LegisInsightJournalEditor@2024!',
                'role' => 'editor_law',
            ],
            [
                'username' => 'admin_oea',
                'email' => 'admin@oeajournal.com',
                'password' => 'OEAJournalAdmin@2024!',
                'role' => 'admin_economy',
            ],
            [
                'username' => 'editor_oea',
                'email' => 'editor@oeajournal.com',
                'password' => 'OEAJournalEditor@2024!',
                'role' => 'editor_economy',
            ],
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
