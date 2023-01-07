<?php

namespace App\Responses;

use Illuminate\Http\JsonResponse;

abstract class BaseResponse
{
    /**
     * @return int
     */
    protected function getHttpStatus(): int
    {
        return 200;
    }

    /**
     * @return JsonResponse
     */
    public function send(): JsonResponse
    {
        return response()->json(
            $this->getPayload(),
            $this->getHttpStatus()
        );
    }

    /**
     * @return array
     */
    abstract protected function getPayload(): array;
}
