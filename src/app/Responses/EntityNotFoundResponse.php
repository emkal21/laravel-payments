<?php

namespace App\Responses;

class EntityNotFoundResponse extends ErrorResponse
{
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

    /**
     * @return int
     */
    protected function getHttpStatus(): int
    {
        return 404;
    }
}
