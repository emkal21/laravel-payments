<?php

namespace Tests\Feature;

use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;

class CreateChargeValidationTest extends AbstractTestCase
{
    /** @var array $testValues */
    private $testValues = [
        'card' => [
            'card_number' => [
                'The card.card number field is required.' => ['', null],
                'The card.card number must be a string.' => [12345, true],
                'The card.card number format is invalid.' => [
                    'this-is-not-a-valid-card-number',
                ],
            ],
            'expiration_date' => [
                'The card.expiration date field is required.' => ['', null],
                'The card.expiration date must be a string.' => [12345, true],
                'The card.expiration date format is invalid.' => [
                    'this-is-not-a-valid-expiration-date',
                    '12-2024',
                    '122024',
                    '12/24',
                    '1234',
                ],
            ],
            'cvv' => [
                'The card.cvv field is required.' => ['', null],
                'The card.cvv must be a string.' => [12345, true],
                'The card.cvv format is invalid.' => [
                    'this-is-not-a-valid-cvv',
                    '1 2 3',
                ],
            ],
            'cardholder_name' => [
                'The card.cardholder name field is required.' => ['', null],
                'The card.cardholder name must be a string.' => [12345, true],
            ],
        ],
        'customer' => [
            'email' => [
                'The customer.email field is required.' => ['', null],
                'The customer.email must be a string.' => [12345, true],
                'The customer.email must be a valid email address.' => [
                    'this-is-not-a-email',
                    'email@email@email.com',
                    'email.com',
                    'email',
                ],
            ],
            'address_line_1' => [
                'The customer.address line 1 field is required.' => ['', null],
                'The customer.address line 1 must be a string.' => [
                    12345,
                    true
                ],
            ],
            'address_city' => [
                'The customer.address city field is required.' => [
                    '',
                    null
                ],
                'The customer.address city must be a string.' => [
                    12345,
                    true
                ],
            ],
            'address_country' => [
                'The customer.address country field is required.' => [
                    '',
                    null
                ],
                'The customer.address country must be a string.' => [
                    12345,
                    true
                ],
                'The customer.address country must be 2 characters.' => [
                    'this-is-not-a-country-code',
                    'Greece',
                    'GRE',
                ],
            ],
        ],
        'root' => [
            'amount' => [
                'The amount field is required.' => ['', null],
                'The amount must be an integer.' => ['test-amount'],
                'The amount must be at least 100.' => [
                    'test-amount',
                    true,
                    99,
                    0,
                    -1,
                ],
            ],
            'description' => [
                'The description field is required.' => ['', null],
                'The description must be a string.' => [12345, true],
            ],
        ],
    ];

    /** @var bool $isInitialized */
    private $isInitialized = false;

    /**
     * @return void
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->isInitialized) {
            $this->truncateAndCreateTestMerchants();
            $this->isInitialized = true;
        }
    }

    /**
     * @return void
     */
    public function test_all_validation_errors_present(): void
    {
        $response = $this->createCharge();

        $this->assertAllValidationErrorsPresent($response);
    }

    /**
     * @return void
     */
    public function test_validation_errors(): void
    {
        foreach ($this->testValues as $parent => $fields) {
            foreach ($fields as $field => $errors) {
                foreach ($errors as $error => $values) {
                    foreach ($values as $value) {
                        $payload = $this->validInput;

                        if ($parent === 'root') {
                            $payload[$field] = $value;
                        } else {
                            $payload[$parent][$field] = $value;
                        }

                        $response = $this->createCharge($payload);

                        $this->assertValidationErrors($response, [$error]);
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function test_valid_input(): void
    {
        $response = $this->createCharge($this->validInput);

        $this->assertSuccessfulResponse($response);
    }
}
