<?php

namespace App\Restify;

use App\Models\AcademicYear;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class AcademicYearRepository extends Repository
{
    public static string $model = AcademicYear::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            id(),
            \field('year')->rules('required', 'regex:/^[0-9]{4}\/[0-9]{4}$/'),
            \field('semester')->rules('required', 'in:odd,even'),
            \field('teacher_id')->rules('required', 'exists:teachers,id'),
        ];
    }
}
