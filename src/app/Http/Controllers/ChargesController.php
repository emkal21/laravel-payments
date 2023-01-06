<?php

namespace App\Http\Controllers;

use App\Responses\SuccessResponse;
use Illuminate\Http\JsonResponse;

class ChargesController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $payload = ['success' => true];

        return (new SuccessResponse($payload))->send();
    }
}
