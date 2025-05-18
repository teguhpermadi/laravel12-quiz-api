<?php

namespace App\Restify;

use App\Models\Teacher;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class TeacherRepository extends Repository
{
    public static string $model = Teacher::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            id(),
            field('name')->rules('required', 'max:255', 'string'),
            field('gender')->rules('required', 'in:male,female'),
        ];
    }
}
