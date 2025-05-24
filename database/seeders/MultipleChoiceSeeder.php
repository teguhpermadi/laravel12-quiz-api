<?php

namespace Database\Seeders;

use App\Models\MultipleChoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MultipleChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MultipleChoice::factory(10)->create(); // Ganti 10 dengan jumlah data yang Anda inginkan untuk ditambahkan ke dalam tabel multiple_choices.
    }
}
