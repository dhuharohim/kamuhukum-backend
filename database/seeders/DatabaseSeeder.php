<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            RoleSeeder::class,
            AnnouncementsTableSeeder::class,
            EditionsTableSeeder::class,
            ArticlesTableSeeder::class,
            ArticleContributorsTableSeeder::class,
            ArticleKeywordsTableSeeder::class,
            ArticleReferencesTableSeeder::class,
            ArticleFilesTableSeeder::class,

            // UserAnnouncementTableSeeder::class,
        ]);
    }
}
