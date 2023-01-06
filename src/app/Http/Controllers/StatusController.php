<?php

namespace App\Http\Controllers;

use App\Responses\SuccessResponse;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $payload = [
            'available' => true,
            'timestamp' => now(),
        ];

        return (new SuccessResponse($payload))->send();
    }
}
