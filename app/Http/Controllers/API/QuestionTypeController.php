<?php

namespace App\Http\Controllers\API;

use App\Enums\QuestionTypeEnum;
use Illuminate\Http\JsonResponse;

class QuestionTypeController extends BaseEnumController
{
    public function index(): JsonResponse
    {
        return $this->enumResponse(QuestionTypeEnum::cases());
    }
}