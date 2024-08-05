<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EditionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('editions')->insert([
            [
                'name_edition' => 'Law Edition 1',
                'edition_for' => 'law',
                'slug' => Str::slug('Law Edition 1'),
                'img_path' => 'images/edition1.jpg',
                'volume' => 1,
                'issue' => 1,
                'year' => 2023,
                'description' => 'This is the description for Law Edition 1.',
                'publish_date' => Carbon::now(),
                'status' => 'Published',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name_edition' => 'Economic Edition 1',
                'edition_for' => 'economic',
                'slug' => Str::slug('Economic Edition 1'),
                'img_path' => 'images/edition2.jpg',
                'volume' => 1,
                'issue' => 1,
                'year' => 2023,
                'description' => 'This is the description for Economic Edition 1.',
                'publish_date' => Carbon::now(),
                'status' => 'Published',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name_edition' => 'Law Edition 2',
                'edition_for' => 'law',
                'slug' => Str::slug('Law Edition 2'),
                'img_path' => 'images/edition3.jpg',
                'volume' => 2,
                'issue' => 1,
                'year' => 2024,
                'description' => 'This is the description for Law Edition 2.',
                'publish_date' => Carbon::now(),
                'status' => 'Draft',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name_edition' => 'Economic Edition 2',
                'edition_for' => 'economic',
                'slug' => Str::slug('Economic Edition 2'),
                'img_path' => 'images/edition4.jpg',
                'volume' => 2,
                'issue' => 1,
                'year' => 2024,
                'description' => 'This is the description for Economic Edition 2.',
                'publish_date' => Carbon::now(),
                'status' => 'Archive',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
