<?php

namespace App\Responses;

class ErrorResponse extends BaseResponse
{
    /* @var string[] $errors */
    protected $errors = [];

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    protected function getPayload(): array
    {
        return ['errors' => $this->errors];
    }

    /**
     * @return int
     */
    protected function getHttpStatus(): int
    {
        return 422;
    }
}
