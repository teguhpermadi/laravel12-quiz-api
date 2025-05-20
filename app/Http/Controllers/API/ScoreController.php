<?php

namespace App\Http\Controllers\API;

use App\Enums\ScoreEnum;
use Illuminate\Http\JsonResponse;

class ScoreController extends BaseEnumController
{
    public function index(): JsonResponse
    {
        return $this->enumResponse(ScoreEnum::cases());
    }
}