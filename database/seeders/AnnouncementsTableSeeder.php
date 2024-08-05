<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnouncementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('announcements')->insert([
            [
                // 'edition_id' => 1,
                'announcement_for' => 'law',
                'slug' => Str::slug('Law Announcement 1'),
                'title' => 'Law Announcement 1',
                'submission_deadline_date' => Carbon::now()->addDays(10),
                'published_date' => Carbon::now(),
                'description' => 'This is the description for Law Announcement 1.',
                'extend_submission_date' => Carbon::now()->addDays(15),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'edition_id' => 2,
                'announcement_for' => 'economic',
                'slug' => Str::slug('Economic Announcement 1'),
                'title' => 'Economic Announcement 1',
                'submission_deadline_date' => Carbon::now()->addDays(20),
                'published_date' => Carbon::now(),
                'description' => 'This is the description for Economic Announcement 1.',
                'extend_submission_date' => Carbon::now()->addDays(25),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'edition_id' => 3,
                'announcement_for' => 'law',
                'slug' => Str::slug('Law Announcement 2'),
                'title' => 'Law Announcement 2',
                'submission_deadline_date' => Carbon::now()->addDays(30),
                'published_date' => Carbon::now(),
                'description' => 'This is the description for Law Announcement 2.',
                'extend_submission_date' => Carbon::now()->addDays(35),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'edition_id' => 4,
                'announcement_for' => 'economic',
                'slug' => Str::slug('Economic Announcement 2'),
                'title' => 'Economic Announcement 2',
                'submission_deadline_date' => Carbon::now()->addDays(40),
                'published_date' => Carbon::now(),
                'description' => 'This is the description for Economic Announcement 2.',
                'extend_submission_date' => Carbon::now()->addDays(45),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
