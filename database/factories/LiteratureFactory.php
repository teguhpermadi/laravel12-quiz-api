<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LiteratureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($literature) {
            $tempDir = storage_path('app/public/media');
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            try {
                // Buat gambar dengan GD Library untuk literature
                $width = 800;
                $height = 600;
                $image = imagecreatetruecolor($width, $height);
                
                // Buat warna acak untuk latar belakang
                $bgColor = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
                imagefill($image, 0, 0, $bgColor);
                
                // Tambahkan beberapa bentuk acak
                for ($i = 0; $i < 5; $i++) {
                    $color = imagecolorallocate($image, rand(0, 150), rand(0, 150), rand(0, 150));
                    imagefilledrectangle(
                        $image,
                        rand(0, $width/2),
                        rand(0, $height/2),
                        rand($width/2, $width),
                        rand($height/2, $height),
                        $color
                    );
                }
                
                // Tambahkan teks judul
                $textColor = imagecolorallocate($image, 0, 0, 0);
                $title = "Literature: " . substr($literature->title, 0, 30);
                imagestring($image, 5, 20, 20, $title, $textColor);
                
                // Tambahkan teks author
                if ($literature->author) {
                    $author = "Author: " . $literature->author;
                    imagestring($image, 4, 20, 50, $author, $textColor);
                }
                
                // Simpan gambar ke file
                $filename = 'literature_' . Str::random(20) . '.jpg';
                $filePath = $tempDir . '/' . $filename;
                imagejpeg($image, $filePath, 90); // 90 adalah kualitas
                imagedestroy($image);
                
                if (file_exists($filePath)) {
                    // Tambahkan media ke koleksi
                    $literature->addMedia($filePath)
                        ->toMediaCollection('literature_media');
                    
                    Log::info('Generated literature image successfully added to media collection', [
                        'literature_id' => $literature->id,
                        'file_path' => $filePath
                    ]);
                } else {
                    Log::error('Generated literature image file was not created successfully', [
                        'file_path' => $filePath
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Literature Media Error:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });
    }
}