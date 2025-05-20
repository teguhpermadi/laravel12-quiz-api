<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseEnumController extends Controller
{
    protected function enumResponse(array $cases): JsonResponse
    {
        $options = array_map(function ($case) {
            return [
                'value' => $case->value,
                'name' => str_replace('_', ' ', $case->name),
                'description' => $case->description()
            ];
        }, $cases);

        return response()->json([
            'status' => 'success',
            'data' => $options
        ]);
    }
}