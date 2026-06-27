<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CompleteAuthSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing users
        User::truncate();

        // Create Admin (role: admin)
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@cbeplatform.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        $this->command->info('✅ Admin: admin / admin123');

        // Create Teacher (role: teacher)
        User::create([
            'name' => 'Teacher User',
            'username' => 'teacher',
            'email' => 'teacher@cbeplatform.test',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
        ]);
        $this->command->info('✅ Teacher: teacher / teacher123');

        // Create Student (role: learner)
        User::create([
            'name' => 'Student User',
            'username' => 'student',
            'email' => 'student@cbeplatform.test',
            'password' => Hash::make('student123'),
            'role' => 'learner',
        ]);
        $this->command->info('✅ Student: student / student123');

        $this->command->newLine();
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('Admin:    http://192.168.0.100:8001/admin/login → admin / admin123');
        $this->command->info('Teacher:  http://192.168.0.100:8001/teacher/login → teacher / teacher123');
        $this->command->info('Student:  http://192.168.0.100:8001/learn/login → student / student123');
    }
}
