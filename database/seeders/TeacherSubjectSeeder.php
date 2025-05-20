<?php

namespace Database\Seeders;

use App\Models\TeacherSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=0; $i < 50; $i++) { 
            try {
                TeacherSubject::factory(1)->create();
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
}
