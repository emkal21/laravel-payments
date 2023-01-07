<?php

namespace App\Billing\PaymentGateways;

use App\Billing\CreditCard;
use App\Billing\CustomerDetails;
use App\Billing\PaymentGatewayResult;

abstract class AbstractPaymentGateway
{
    /** @var string $secretKey */
    protected $secretKey = '';

    /** @var bool $isTest */
    protected $isTest = true;

    /** @var string $verificationStringQueryParam */
    protected $verificationStringQueryParam = '';

    /** @var string CHARGE_GENERIC_ERROR */
    protected const CHARGE_GENERIC_ERROR =
        'The provided payment method has been declined.';

    /** @var array CHARGE_ERROR_CODES */
    protected const CHARGE_ERROR_CODES = [];

    /**
     * @param string $secretKey
     * @param bool $isTest
     */
    public function __construct(
        string $secretKey,
        bool $isTest = true
    ) {
        $this->secretKey = $secretKey;
        $this->isTest = $isTest;
    }

    /**
     * @return string
     */
    protected function getDefaultCurrency(): string
    {
        return config('currencies.default_currency', 'eur');
    }

    /**
     * @return string
     */
    public function getVerificationStringQueryParam(): string
    {
        return $this->verificationStringQueryParam;
    }

    /**
     * @param string $errorCode
     * @return string
     */
    protected function mapErrorCode(string $errorCode): string
    {
        return array_key_exists($errorCode, static::CHARGE_ERROR_CODES)
            ? static::CHARGE_ERROR_CODES[$errorCode]
            : static::CHARGE_GENERIC_ERROR;
    }

    /**
     * @return PaymentGatewayResult
     */
    protected function getGenericErrorResult(): PaymentGatewayResult
    {
        $message = 'An unknown error occurred while processing this charge.';

        return new PaymentGatewayResult(false, $message);
    }

    /**
     * @param CreditCard $creditCard
     * @param CustomerDetails $customerDetails
     * @param int $amount
     * @param string $description
     * @param string $returnUrl
     * @return PaymentGatewayResult
     */
    abstract public function createCharge(
        CreditCard $creditCard,
        CustomerDetails $customerDetails,
        int $amount,
        string $description,
        string $returnUrl
    ): PaymentGatewayResult;

    /**
     * @param string $verificationString
     * @return PaymentGatewayResult
     */
    abstract public function verifyCharge(
        string $verificationString
    ): PaymentGatewayResult;
}
