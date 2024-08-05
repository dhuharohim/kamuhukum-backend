<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArticlesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('articles')->insert([
            [
                // 'user_id' => 1,
                'uuid' => Str::uuid(),
                'edition_id' => 1,
                'article_for' => 'law',
                'prefix' => 'Mr.',
                'title' => 'Law Article 1',
                'subtitle' => 'Subtitle of Law Article 1',
                'section' => 'article',
                'status' => 'submission',
                'comments_for_editor' => 'No comments.',
                'abstract' => 'Abstract for Law Article 1.',
                'slug' => Str::slug('Law Article 1'),
                'downloaded' => 10,
                'viewed' => 100,
                'doi_link' => 'https://doi.org/10.1234/lawarticle1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'user_id' => 2,
                'uuid' => Str::uuid(),
                'edition_id' => 2,
                'article_for' => 'economic',
                'prefix' => 'Dr.',
                'title' => 'Economic Article 1',
                'subtitle' => 'Subtitle of Economic Article 1',
                'section' => 'general_article',
                'status' => 'review',
                'comments_for_editor' => 'Needs revision.',
                'abstract' => 'Abstract for Economic Article 1.',
                'slug' => Str::slug('Economic Article 1'),
                'downloaded' => 20,
                'viewed' => 200,
                'doi_link' => 'https://doi.org/10.1234/economicarticle1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'user_id' => 3,
                'uuid' => Str::uuid(),
                'edition_id' => 3,
                'article_for' => 'law',
                'prefix' => 'Ms.',
                'title' => 'Law Article 2',
                'subtitle' => 'Subtitle of Law Article 2',
                'section' => 'article',
                'status' => 'production',
                'comments_for_editor' => 'Ready for production.',
                'abstract' => 'Abstract for Law Article 2.',
                'slug' => Str::slug('Law Article 2'),
                'downloaded' => 30,
                'viewed' => 300,
                'doi_link' => 'https://doi.org/10.1234/lawarticle2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                // 'user_id' => 4,
                'uuid' => Str::uuid(),
                'edition_id' => 4,
                'article_for' => 'economic',
                'prefix' => 'Prof.',
                'title' => 'Economic Article 2',
                'subtitle' => 'Subtitle of Economic Article 2',
                'section' => 'general_article',
                'status' => 'incomplete',
                'comments_for_editor' => 'Incomplete article.',
                'abstract' => 'Abstract for Economic Article 2.',
                'slug' => Str::slug('Economic Article 2'),
                'downloaded' => 40,
                'viewed' => 400,
                'doi_link' => 'https://doi.org/10.1234/economicarticle2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
