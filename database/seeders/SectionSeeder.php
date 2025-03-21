<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['name' => 'Tentang'],
            ['name' => 'Pengajuan'],
            ['name' => 'Terkini'],
            ['name' => 'Arsip'],
            ['name' => 'Pengumuman'],
            ['name' => 'Kontak'],
        ];

        Section::insert(array_map(function ($section) {
            return array_merge($section, [
                'slug' => strtolower($section['name']),
                'position' => 'main',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }, $sections));
    }
}
