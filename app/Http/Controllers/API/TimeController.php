<?php

namespace App\Http\Controllers\API;

use App\Enums\TimeEnum;
use Illuminate\Http\JsonResponse;

class TimeController extends BaseEnumController
{
    public function index(): JsonResponse
    {
        return $this->enumResponse(TimeEnum::cases());
    }
}