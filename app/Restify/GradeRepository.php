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
            \field('name')->rules('required', 'max:255'),
            \field('level')->rules('required', 'numeric', 'min:1', 'max:12'),
        ];
    }
}
