<?php

namespace Database\Seeders;

use App\Models\Literature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiteratureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Literature::factory(20)->create();
    }
}