<?php

namespace App\Responses;

class NotAuthenticatedResponse extends ErrorResponse
{
    /** @var string $message */
    protected $message;

    /**
     * @param string $message
     */
    public function __construct(
        string $message = 'Authentication is required to access this resource.'
    ) {
        parent::__construct([$message]);

        $this->message = $message;
    }

    /**
     * @return int
     */
    protected function getHttpStatus(): int
    {
        return 401;
    }
}
