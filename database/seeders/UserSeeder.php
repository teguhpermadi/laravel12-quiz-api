<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create admin user
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        // assign admin role
        $admin->assignRole('admin');

        // create teacher user
        $teacher = User::create([
            'name' => 'teacher',
            'email' => 'teacher@teacher.com',
            'password' => bcrypt('password'),
        ]);
        // assign teacher role
        $teacher->assignRole('teacher');

        // create student user
        $student = User::create([
            'name' => 'student',
            'email' => 'student@student.com',
            'password' => bcrypt('password'),
        ]);
        // assign student role
        $student->assignRole('student');
    }
}
