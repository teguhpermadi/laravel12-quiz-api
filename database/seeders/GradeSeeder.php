<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=0; $i < 6; $i++) { 
            \App\Models\Grade::create([
                'name' => 'Grade ' . $i,
                'level' => $i,
            ]);
        }
    }
}
