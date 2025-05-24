<?php

namespace Database\Seeders;

use App\Models\ComplexMultipleChoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplexMultipleChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ComplexMultipleChoice::factory(10)->create();
    }
}
