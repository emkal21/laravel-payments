<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class BaseValidator
{
    /** @var array $input */
    protected $input = [];

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function validate(): array
    {
        $validator = Validator::make(
            $this->input,
            $this->getRules(),
            $this->getMessages(),
            $this->getAttributes(),
        );

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return [];
    }
}
