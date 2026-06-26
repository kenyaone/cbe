<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CurriculumTypeSeeder::class);
        $this->call(PP1CurriculumSeeder::class);
        $this->call(PP1MetadataSeeder::class);
        $this->call(PP1CompleteMetadataSeeder::class);
        $this->call(PP1InteractivesAndPDFSeeder::class);

        User::factory()->create([
            'name' => 'Test Teacher',
            'email' => 'teacher@school.com',
            'role' => 'teacher'
        ]);

        User::factory()->create([
            'name' => 'Test Learner',
            'email' => 'learner@school.com',
            'role' => 'learner'
        ]);
    }
}
