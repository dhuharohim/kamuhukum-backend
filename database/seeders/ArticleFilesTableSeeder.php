<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleFilesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('article_files')->insert([
            [
                'article_id' => 1,
                'file_name' => 'law_article_1.pdf',
                'file_path' => 'files/law_article_1.pdf',
                'type' => 'Article Text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 2,
                'file_name' => 'economic_article_1.pdf',
                'file_path' => 'files/economic_article_1.pdf',
                'type' => 'Article Text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 3,
                'file_name' => 'justice_article_1.pdf',
                'file_path' => 'files/justice_article_1.pdf',
                'type' => 'Article Text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 4,
                'file_name' => 'finance_article_1.pdf',
                'file_path' => 'files/finance_article_1.pdf',
                'type' => 'Article Text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
