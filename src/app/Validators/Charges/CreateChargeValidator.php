<?php

namespace App\Validators\Charges;

use App\Validators\BaseValidator;

class CreateChargeValidator extends BaseValidator
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'card' => [
                'required',
                'array',
            ],
            'card.card_number' => [
                'required',
                'string',
                'regex:/^\d+$/',
            ],
            'card.expiration_date' => [
                'required',
                'string',
                'regex:/^\d{2}\/\d{4}$/',
            ],
            'card.cvv' => [
                'required',
                'string',
                'regex:/^\d+$/',
            ],
            'card.cardholder_name' => [
                'required',
                'string',
            ],
            'customer' => [
                'required',
                'array',
            ],
            'customer.email' => [
                'required',
                'string',
                'email',
            ],
            'customer.address_line_1' => [
                'required',
                'string',
            ],
            'customer.address_city' => [
                'required',
                'string',
            ],
            'customer.address_country' => [
                'required',
                'string',
                'size:2'
            ],
            'amount' => [
                'required',
                'integer',
                'min:100',
            ],
            'description' => [
                'required',
                'string',
            ],
        ];
    }
}
