<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleContributorsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('article_contributors')->insert([
            [
                'article_id' => 1,
                'contributor_role' => 'author',
                'given_name' => 'John',
                'family_name' => 'Doe',
                'phone' => '1234567890',
                'contact' => 'john.doe@example.com',
                'preferred_name' => 'John Doe',
                'affilation' => 'Law University',
                'country' => 'USA',
                'img_url' => 'images/john_doe.jpg',
                'homepage_url' => 'http://johndoe.com',
                'orcid_id' => '0000-0001-2345-6789',
                'mailing_address' => '123 Main St, Anytown, USA',
                'bio_statement' => 'John Doe is a law professor.',
                'reviewing_interest' => 'Criminal Law',
                'principal_contact' => 1,
                'in_browse_list' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 2,
                'contributor_role' => 'author',
                'given_name' => 'Jane',
                'family_name' => 'Smith',
                'phone' => '0987654321',
                'contact' => 'jane.smith@example.com',
                'preferred_name' => 'Jane Smith',
                'affilation' => 'Economic University',
                'country' => 'UK',
                'img_url' => 'images/jane_smith.jpg',
                'homepage_url' => 'http://janesmith.com',
                'orcid_id' => '0000-0002-3456-7890',
                'mailing_address' => '456 Market St, Anycity, UK',
                'bio_statement' => 'Jane Smith is an economics professor.',
                'reviewing_interest' => 'Market Analysis',
                'principal_contact' => 1,
                'in_browse_list' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 3,
                'contributor_role' => 'author',
                'given_name' => 'Alice',
                'family_name' => 'Johnson',
                'phone' => '1231231234',
                'contact' => 'alice.johnson@example.com',
                'preferred_name' => 'Alice Johnson',
                'affilation' => 'Justice University',
                'country' => 'Canada',
                'img_url' => 'images/alice_johnson.jpg',
                'homepage_url' => 'http://alicejohnson.com',
                'orcid_id' => '0000-0003-4567-8901',
                'mailing_address' => '789 Main St, Anytown, Canada',
                'bio_statement' => 'Alice Johnson is a justice professor.',
                'reviewing_interest' => 'Constitutional Law',
                'principal_contact' => 1,
                'in_browse_list' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'article_id' => 4,
                'contributor_role' => 'author',
                'given_name' => 'Bob',
                'family_name' => 'Williams',
                'phone' => '4564564567',
                'contact' => 'bob.williams@example.com',
                'preferred_name' => 'Bob Williams',
                'affilation' => 'Finance University',
                'country' => 'Australia',
                'img_url' => 'images/bob_williams.jpg',
                'homepage_url' => 'http://bobwilliams.com',
                'orcid_id' => '0000-0004-5678-9012',
                'mailing_address' => '101 Market St, Anycity, Australia',
                'bio_statement' => 'Bob Williams is a finance professor.',
                'reviewing_interest' => 'Corporate Finance',
                'principal_contact' => 1,
                'in_browse_list' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
