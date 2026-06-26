<?php

namespace Database\Seeders;

use App\Models\CurriculumType;
use Illuminate\Database\Seeder;

class CurriculumTypeSeeder extends Seeder
{
    public function run(): void
    {
        CurriculumType::create([
            'name' => 'CBE',
            'description' => 'Competency-Based Education (PP1 - Grade 12)'
        ]);

        CurriculumType::create([
            'name' => '8-4-4',
            'description' => '8-4-4 System (Form 3-4)'
        ]);
    }
}
