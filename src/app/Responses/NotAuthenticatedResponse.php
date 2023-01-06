<?php

namespace App\Responses;

class NotAuthenticatedResponse extends ErrorResponse
{
    /** @var int $httpStatus */
    protected $httpStatus = 401;

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
}
