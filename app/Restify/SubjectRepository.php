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
            \field('name')->rules('required','max:255'),
            \field('code')->rules('required','max:10','min:10','unique:subjects,code'),
        ];
    }
}
