<?php

namespace Tests\Feature;

use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;
use App\Services\ApiTokensService;
use App\Services\MerchantsImportService;
use App\Services\MerchantsService;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /** @var MerchantsService $merchantsService */
    protected $merchantsService;

    /** @var ApiTokensService $apiTokensService */
    protected $apiTokensService;

    /** @var MerchantsImportService $merchantsImportService */
    protected $merchantsImportService;

    /** @var string MERCHANT_USERNAME_STRIPE */
    protected const MERCHANT_USERNAME_STRIPE = 'test_stripe';

    /** @var string MERCHANT_USERNAME_PINPAYMENTS */
    protected const MERCHANT_USERNAME_PINPAYMENTS = 'test_pinpayments';

    /** @var string MERCHANT_API_TOKEN */
    protected const MERCHANT_API_TOKEN = 'secret-api-token';

    /** @var array $validInput */
    protected $validInput = [
        'card' => [
            'card_number' => '4242424242424242',
            'expiration_date' => '12/2024',
            'cvv' => '123',
            'cardholder_name' => 'John Doe',
        ],
        'customer' => [
            'email' => 'email@email.com',
            'address_line_1' => 'Test address',
            'address_city' => 'Test city',
            'address_country' => 'GR',
        ],
        'amount' => 100,
        'description' => 'Test description'
    ];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->merchantsService = $this->app->make(MerchantsService::class);
        $this->apiTokensService = $this->app->make(ApiTokensService::class);
        $this->merchantsImportService = $this->app->make(MerchantsImportService::class);
    }

    /**
     * @param TestResponse $response
     * @param array $errors
     * @return void
     */
    protected function assertValidationErrors(
        TestResponse $response,
        array $errors
    ): void {
        $response
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) use ($errors) {
                $json->has('errors');
            });

        $actualErrors = $response->json('errors');

        foreach ($errors as $error) {
            $this->assertTrue(in_array($error, $actualErrors));
        }
    }

    /**
     * @param TestResponse $response
     * @return void
     */
    protected function assertAllValidationErrorsPresent(
        TestResponse $response
    ): void {
        $errors = [
            'The card field is required.',
            'The card.card number field is required.',
            'The card.expiration date field is required.',
            'The card.cvv field is required.',
            'The card.cardholder name field is required.',
            'The customer field is required.',
            'The customer.email field is required.',
            'The customer.address line 1 field is required.',
            'The customer.address city field is required.',
            'The customer.address country field is required.',
            'The amount field is required.',
            'The description field is required.',
        ];

        $this->assertValidationErrors($response, $errors);

        $actualErrors = $response->json('errors');

        $this->assertCount(count($errors), $actualErrors);
    }

    /**
     * @return void
     */
    protected function truncateTables(): void
    {
        $this->merchantsService->truncate();
        $this->apiTokensService->truncate();
    }

    /**
     * @param array $payload
     * @param array $headers
     * @return TestResponse
     */
    protected function getCreateResponse(
        array $payload = [],
        array $headers = []
    ): TestResponse {
        return $this->postJson('/api/charges', $payload, $headers);
    }

    /**
     * @param array $payload
     * @param string $username
     * @param string $password
     * @return TestResponse
     */
    protected function getAuthenticatedCreateResponse(
        array $payload = [],
        string $username = '',
        string $password = ''
    ): TestResponse {
        $authString = $this->encodeHttpBasicAuthCredentials(
            $username,
            $password
        );

        return $this->getCreateResponse($payload, [
            'Authorization' => 'Basic ' . $authString,
        ]);
    }

    /**
     * @param TestResponse $response
     * @return void
     */
    protected function assertFailedAuthenticationResponse(
        TestResponse $response
    ) {
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [
                    'Authentication is required to access this resource.',
                ],
            ]);
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    protected function encodeHttpBasicAuthCredentials(
        string $username,
        string $password
    ): string {
        return base64_encode(sprintf('%s:%s', $username, $password));
    }

    /**
     * @param bool $createApiTokens
     * @return array
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    protected function createTestMerchants(bool $createApiTokens = true): array
    {
        $path = 'test-data/merchants.json';

        return $this
            ->merchantsImportService
            ->importFromFile($path, $createApiTokens);
    }

    /**
     * @param bool $createApiTokens
     * @return array
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    protected function truncateAndCreateTestMerchants(
        bool $createApiTokens = true
    ): array {
        $this->truncateTables();
        $merchants = $this->createTestMerchants($createApiTokens);
        $this->assertIsArray($merchants);
        $this->assertNotEmpty($merchants);

        return $merchants;
    }

    /**
     * @param array $payload
     * @param string $username
     * @return TestResponse
     */
    protected function createCharge(
        array $payload = [],
        string $username = self::MERCHANT_USERNAME_STRIPE
    ): TestResponse {
        return $this->getAuthenticatedCreateResponse(
            $payload,
            $username,
            self::MERCHANT_API_TOKEN,
        );
    }

    /**
     * @param TestResponse $response
     * @return void
     */
    protected function assertSuccessfulResponse(TestResponse $response): void
    {
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'isSuccess' => true,
                'errorMessage' => null,
                'isFurtherActionRequired' => false,
                'furtherActionUrl' => null,
            ]);
    }

    /**
     * @param TestResponse $response
     * @return void
     */
    protected function assertCardDeclinedResponse(TestResponse $response): void
    {
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'isSuccess' => false,
                'errorMessage' => 'The provided payment method has been declined.',
                'isFurtherActionRequired' => false,
                'furtherActionUrl' => null,
            ]);
    }

    /**
     * @param TestResponse $response
     * @return void
     */
    protected function assertRequires3DSResponse(TestResponse $response): void
    {
        $response
            ->assertStatus(202)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->where('isSuccess', false)
                    ->where('errorMessage', null)
                    ->where('isFurtherActionRequired', true)
                    ->has('furtherActionUrl')
                    ->whereType('furtherActionUrl', 'string')
                    ->etc();
            });
    }
}
