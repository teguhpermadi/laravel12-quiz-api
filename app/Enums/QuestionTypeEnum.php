<?php

namespace App\Enums;

enum QuestionTypeEnum: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case COMPLEX_MULTIPLE_CHOICE = 'complex_multiple_choice';
    case TRUE_FALSE = 'true_false';
    case SHORT_ANSWER = 'short_answer';
    case ESSAY = 'essay';
    case MATH_INPUT = 'math_input';
    case MATCHING = 'matching';
    case SEQUENCE = 'sequence';
    case WORD_CLOUD = 'word_cloud';
    case DRAWING = 'drawing';
    case GRAPH = 'graph';
    case HOTSPOT = 'hotspot';
    case VOICE_RESPONSE = 'voice_response';
    case IMAGE_RESPONSE = 'image_response';
    case VIDEO_RESPONSE = 'video_response';
    case CATEGORIZATION = 'categorization';
    case ARABIC_RESPONSE = 'arabic_response';
    case JAVANESE_RESPONSE = 'javanese_response';

    public function description(): string
    {
        return match($this) {
            self::MULTIPLE_CHOICE => 'Pilihan ganda dengan satu jawaban benar',
            self::COMPLEX_MULTIPLE_CHOICE => 'Pilihan ganda dengan beberapa jawaban benar',
            self::TRUE_FALSE => 'Pertanyaan benar/salah',
            self::SHORT_ANSWER => 'Isian singkat dengan jawaban teks pendek',
            self::ESSAY => 'Jawaban panjang berupa esai',
            self::MATH_INPUT => 'Jawaban berupa input matematika',
            self::MATCHING => 'Menjodohkan antara dua kelompok item',
            self::SEQUENCE => 'Menyusun urutan jawaban yang benar',
            self::WORD_CLOUD => 'Jawaban berupa kumpulan kata (word cloud)',
            self::DRAWING => 'Jawaban berupa gambar yang digambar',
            self::GRAPH => 'Jawaban berupa grafik atau diagram',
            self::HOTSPOT => 'Memilih area pada gambar (hotspot)',
            self::VOICE_RESPONSE => 'Jawaban berupa rekaman suara',
            self::IMAGE_RESPONSE => 'Jawaban berupa unggahan gambar',
            self::VIDEO_RESPONSE => 'Jawaban berupa unggahan video',
            self::CATEGORIZATION => 'Mengelompokkan item ke dalam kategori',
            self::ARABIC_RESPONSE => 'Jawaban berupa teks dalam bahasa Arab',
            self::JAVANESE_RESPONSE => 'Jawaban berupa teks dalam bahasa Jawa',
        };
    }
}