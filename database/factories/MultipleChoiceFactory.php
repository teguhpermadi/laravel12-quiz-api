<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MultipleChoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question_id' => Question::get()->random()->id,
            'choice' => $this->faker->sentence(),
            'is_correct' => $this->faker->boolean(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($choice) {
            $tempDir = storage_path('app/public/media');
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            try {
                // Buat gambar dengan GD Library
                $width = 400;
                $height = 200;
                $image = imagecreatetruecolor($width, $height);
                
                // Warna latar belakang acak
                $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefill($image, 0, 0, $bgColor);
                
                // Tambahkan bentuk-bentuk acak
                for ($i = 0; $i < 5; $i++) {
                    $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                    imagefilledrectangle(
                        $image,
                        rand(0, $width),
                        rand(0, $height),
                        rand(0, $width),
                        rand(0, $height),
                        $color
                    );
                }
                
                // Tambahkan teks pilihan
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $text = "Choice: " . substr($choice->choice, 0, 30);
                imagestring($image, 5, 20, 20, $text, $textColor);
                
                // Tambahkan info pertanyaan jika ada
                if ($choice->question) {
                    $qText = "Q: " . substr($choice->question->question, 0, 30);
                    imagestring($image, 4, 20, 50, $qText, $textColor);
                }
                
                // Simpan gambar ke file
                $filename = Str::random(40) . '.jpg';
                $filePath = $tempDir . '/' . $filename;
                imagejpeg($image, $filePath, 90);
                imagedestroy($image);
                
                if (file_exists($filePath)) {
                    // Tambahkan media ke koleksi
                    $choice->addMedia($filePath)
                        ->toMediaCollection('choice_media');
                    
                    Log::info('Generated choice image added to media collection', [
                        'choice_id' => $choice->id,
                        'file_path' => $filePath
                    ]);
                } else {
                    Log::error('Failed to generate choice image', [
                        'choice_id' => $choice->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Choice Media Error:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'choice_id' => $choice->id ?? null
                ]);
            }
        });
    }
}
