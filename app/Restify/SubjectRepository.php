<?php

namespace App\Restify;

use App\Models\Subject;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class SubjectRepository extends Repository
{
    public static string $model = Subject::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            id(),
            \field('name'),
            \field('code'),
        ];
    }
}
