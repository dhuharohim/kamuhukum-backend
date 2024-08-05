<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleReferencesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('article_references')->insert([
            [
                'article_id' => 1,
                'reference' => 'Reference 1 for Law Article',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 2,
                'reference' => 'Reference 1 for Economic Article',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 3,
                'reference' => 'Reference 2 for Law Article',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 4,
                'reference' => 'Reference 2 for Economic Article',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
