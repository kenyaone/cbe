<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Learner;
use Illuminate\Support\Facades\Hash;

class CreateTestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin Account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@cbeplatform.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'school_id' => null,
            ]
        );
        $this->command->info('✓ Admin account created: admin / admin123');

        // Create Teacher Account
        User::updateOrCreate(
            ['username' => 'teacher'],
            [
                'name' => 'Teacher User',
                'username' => 'teacher',
                'email' => 'teacher@cbeplatform.test',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'school_id' => null,
            ]
        );
        $this->command->info('✓ Teacher account created: teacher / teacher123');

        // Create Student (Learner) Account
        Learner::updateOrCreate(
            ['username' => 'student'],
            [
                'name' => 'Student User',
                'username' => 'student',
                'email' => 'student@cbeplatform.test',
                'password' => Hash::make('student123'),
                'grade_level' => 'Grade One',
                'admission_number' => 'STU001',
                'phone' => '+254712345678',
            ]
        );
        $this->command->info('✓ Student account created: student / student123');

        $this->command->newLine();
        $this->command->info('=== TEST ACCOUNTS CREATED ===');
        $this->command->newLine();

        $this->command->info('ADMIN:');
        $this->command->line('  Username: admin');
        $this->command->line('  Password: admin123');
        $this->command->line('  Email: admin@cbeplatform.test');
        $this->command->line('  Access: / (Admin Dashboard)');
        $this->command->newLine();

        $this->command->info('TEACHER:');
        $this->command->line('  Username: teacher');
        $this->command->line('  Password: teacher123');
        $this->command->line('  Email: teacher@cbeplatform.test');
        $this->command->line('  Access: / (Curriculum Browser)');
        $this->command->newLine();

        $this->command->info('STUDENT:');
        $this->command->line('  Username: student');
        $this->command->line('  Password: student123');
        $this->command->line('  Email: student@cbeplatform.test');
        $this->command->line('  Grade Level: Grade One');
        $this->command->line('  Access: /learn (Learner Portal)');
        $this->command->newLine();
    }
}
