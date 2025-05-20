<?php

namespace App\Http\Controllers\API;

use App\Enums\QuestionTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class QuestionTypeController extends Controller
{
    /**
     * Get all question types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $questionTypes = [];
        
        foreach (QuestionTypeEnum::cases() as $type) {
            $questionTypes[] = [
                'value' => $type->value,
                'name' => str_replace('_', ' ', ucfirst($type->value)),
                'description' => $type->description()
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $questionTypes
        ]);
    }
}