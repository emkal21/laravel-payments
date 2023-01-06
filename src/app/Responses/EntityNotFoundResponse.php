<?php

namespace App\Responses;

class EntityNotFoundResponse extends ErrorResponse
{
    /** @var int $httpStatus */
    protected $httpStatus = 404;

    /** @var string $message */
    protected $message;

    /**
     * @param string $message
     */
    public function __construct(string $message = 'Entity not found.')
    {
        parent::__construct([$message]);

        $this->message = $message;
    }
}
