<?php

namespace App\Restify;

use App\Models\Grade;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class GradeRepository extends Repository
{
    public static string $model = Grade::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            id(),
            \field('name'),
            \field('level'),
        ];
    }
}
