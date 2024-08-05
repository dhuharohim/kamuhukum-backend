<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleKeywordsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('article_keywords')->insert([
            [
                'article_id' => 1,
                'keyword' => 'Law',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 2,
                'keyword' => 'Economics',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 3,
                'keyword' => 'Justice',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 4,
                'keyword' => 'Finance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
