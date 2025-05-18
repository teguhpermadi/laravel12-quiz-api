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
            field('name'),
            field('gender'),
        ];
    }
}
