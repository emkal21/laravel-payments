<?php

namespace Tests\Feature;

use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;
use Closure;
use Illuminate\Testing\TestResponse;

abstract class AbstractPaymentGatewayTestCase extends AbstractTestCase
{
    /** @var bool $isInitialized */
    private $isInitialized = false;

    /** @var string $username */
    protected $username = '';

    /** @var string CARD_NUMBER_SUCCESS */
    protected const CARD_NUMBER_SUCCESS = '';

    /** @var string CARD_NUMBER_DECLINED */
    protected const CARD_NUMBER_DECLINED = '';

    /** @var string CARD_NUMBER_3DS */
    protected const CARD_NUMBER_3DS = '';

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
     * @param string $cardNumber
     * @param Closure $fnCallback
     * @return void
     */
    private function createChargeWithCard(
        string $cardNumber,
        Closure $fnCallback
    ): void {
        $payload = $this->validInput;

        $payload['card']['card_number'] = $cardNumber;

        $response = $this->createCharge($payload, $this->username);

        $fnCallback($response);
    }

    /**
     * @return void
     */
    public function test_successful_charge(): void
    {
        $this->createChargeWithCard(
            static::CARD_NUMBER_SUCCESS,
            function (TestResponse $response) {
                $this->assertSuccessfulResponse($response);
            }
        );
    }

    /**
     * @return void
     */
    public function test_card_declined(): void
    {
        $this->createChargeWithCard(
            static::CARD_NUMBER_DECLINED,
            function (TestResponse $response) {
                $this->assertCardDeclinedResponse($response);
            }
        );
    }

    /**
     * @return void
     */
    public function test_three_d_s_is_required(): void
    {
        $this->createChargeWithCard(
            static::CARD_NUMBER_3DS,
            function (TestResponse $response) {
                $this->assertRequires3DSResponse($response);
            }
        );
    }
}
