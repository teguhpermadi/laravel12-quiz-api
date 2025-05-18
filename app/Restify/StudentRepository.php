<?php

namespace App\Restify;

use App\Models\Student;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class StudentRepository extends Repository
{
    public static string $model = Student::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            id(),
            \field('name')->rules('required','max:255'),
            \field('gender')->rules('required','in:male,female'),
            \field('nisn')->rules('required','max:10','min:10','unique:students,nisn'),
            \field('nis')->rules('required','max:8','min:8','unique:students,nis'),
        ];
    }
}
