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
            \field('name'),
            \field('gender'),
            \field('nisn'),
            \field('nis'),
        ];
    }
}
