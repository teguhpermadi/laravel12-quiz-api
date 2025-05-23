<?php

namespace Database\Factories;

use App\Enums\QuestionTypeEnum;
use App\Enums\ScoreEnum;
use App\Enums\TimeEnum;
use App\Models\Literature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'question_type' => $this->faker->randomElement(QuestionTypeEnum::cases()),
            'teacher_id' => \App\Models\Teacher::factory(),
            'time' => $this->faker->randomElement(TimeEnum::cases()),
            'score' => $this->faker->randomElement(ScoreEnum::cases()),
            'literature_id' => $this->faker->optional(0.3)->randomElement(Literature::pluck('id')->toArray()), // 30% kemungkinan memiliki literature
        ];
    }

    // Sisanya tetap sama seperti sebelumnya
    public function configure()
    {
        return $this->afterCreating(function ($question) {
            $tempDir = storage_path('app/public/media');
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            try {
                // Buat gambar dengan GD Library
                $width = 640;
                $height = 480;
                $image = imagecreatetruecolor($width, $height);
                
                // Buat warna acak untuk latar belakang
                $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefill($image, 0, 0, $bgColor);
                
                // Tambahkan beberapa bentuk acak
                for ($i = 0; $i < 10; $i++) {
                    $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                    imagefilledellipse(
                        $image,
                        rand(0, $width),
                        rand(0, $height),
                        rand(20, 100),
                        rand(20, 100),
                        $color
                    );
                }
                
                // Tambahkan teks
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $text = "Question ID: " . $question->id;
                imagestring($image, 5, 20, 20, $text, $textColor);
                
                // Jika ada literature, tambahkan informasi literature
                if ($question->literature_id) {
                    $litText = "Literature: " . substr($question->literature->title ?? 'Unknown', 0, 20);
                    imagestring($image, 4, 20, 50, $litText, $textColor);
                }
                
                // Simpan gambar ke file
                $filename = Str::random(40) . '.jpg';
                $filePath = $tempDir . '/' . $filename;
                imagejpeg($image, $filePath, 90); // 90 adalah kualitas
                imagedestroy($image);
                
                if (file_exists($filePath)) {
                    // Tambahkan media ke koleksi
                    $question->addMedia($filePath)
                        ->toMediaCollection('question_media');
                    
                    Log::info('Generated image successfully added to media collection', [
                        'question_id' => $question->id,
                        'file_path' => $filePath
                    ]);
                } else {
                    Log::error('Generated image file was not created successfully', [
                        'file_path' => $filePath
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Media Error:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });
    }
}
