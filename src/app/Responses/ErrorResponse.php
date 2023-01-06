<?php

namespace App\Responses;

class ErrorResponse extends BaseResponse
{
    /** @var int $httpStatus */
    protected $httpStatus = 422;

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
}
