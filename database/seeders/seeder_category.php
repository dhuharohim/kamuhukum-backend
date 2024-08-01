<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class seeder_category extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'science',
            'nature',
            'math',
            'pysch',
            'technologies'
        ];

        foreach($categories as $category){
            Category::create([
                "name" => $category
            ]);
        }
    }
}
