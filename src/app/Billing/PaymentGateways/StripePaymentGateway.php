<?php

namespace App\Billing\PaymentGateways;

use App\Billing\CreditCard;
use App\Billing\CustomerDetails;
use App\Billing\PaymentGatewayResult;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\StripeClient;

class StripePaymentGateway extends AbstractPaymentGateway
{
    /** @var string $verificationStringQueryParam */
    protected $verificationStringQueryParam = 'payment_intent';

    /** @var StripeClient $stripeClient */
    private $stripeClient;

    /** @var string PAYMENT_INTENT_STATUS_SUCCEEDED */
    private const PAYMENT_INTENT_STATUS_SUCCEEDED = 'succeeded';

    /** @var string PAYMENT_INTENT_NEXT_ACTION_TYPE_REDIRECT */
    private const PAYMENT_INTENT_NEXT_ACTION_TYPE_REDIRECT = 'redirect_to_url';

    /** @var array CHARGE_ERROR_CODES */
    protected const CHARGE_ERROR_CODES = [
        'payment_intent_authentication_failure' =>
            'The provided payment method has failed authentication.',
        'card_declined' => self::CHARGE_GENERIC_ERROR,
    ];

    /**
     * @param string $secretKey
     * @param bool $isTest
     */
    public function __construct(
        string $secretKey,
        bool $isTest = true
    ) {
        parent::__construct($secretKey, $isTest);

        $this->stripeClient = new StripeClient($this->secretKey);
    }

    /**
     * @param CreditCard $creditCard
     * @return PaymentMethod
     * @throws ApiErrorException
     */
    private function createPaymentMethod(CreditCard $creditCard): PaymentMethod
    {
        return $this->stripeClient->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => $creditCard->getCardNumber(),
                'exp_month' => $creditCard->getExpirationMonth(),
                'exp_year' => $creditCard->getExpirationYear(),
                'cvc' => $creditCard->getCvv(),
            ],
        ]);
    }

    /**
     * @param CustomerDetails $customerDetails
     * @return Customer
     * @throws ApiErrorException
     */
    private function createCustomer(CustomerDetails $customerDetails): Customer
    {
        return $this->stripeClient->customers->create([
            'email' => $customerDetails->getEmail(),
            'address' => [
                'line1' => $customerDetails->getAddressLine1(),
                'city' => $customerDetails->getAddressCity(),
                'country' => $customerDetails->getAddressCountry(),
            ],
        ]);
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @param Customer $customer
     * @param int $amount
     * @param string $description
     * @param string|null $returnUrl
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    private function createPaymentIntent(
        PaymentMethod $paymentMethod,
        Customer $customer,
        int $amount,
        string $description,
        string $returnUrl = null
    ): PaymentIntent {
        return $this->stripeClient->paymentIntents->create([
            'amount' => $amount,
            'currency' => $this->getDefaultCurrency(),
            'payment_method' => $paymentMethod->id,
            'customer' => $customer->id,
            'description' => $description,
            'confirm' => true,
            'return_url' => $returnUrl,
        ]);
    }

    /**
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    private function retrievePaymentIntent(
        string $paymentIntentId
    ): PaymentIntent {
        return $this->stripeClient->paymentIntents->retrieve($paymentIntentId);
    }

    /**
     * @param CreditCard $creditCard
     * @param CustomerDetails $customerDetails
     * @param int $amount
     * @param string $description
     * @param string $returnUrl
     * @return PaymentGatewayResult
     */
    public function createCharge(
        CreditCard $creditCard,
        CustomerDetails $customerDetails,
        int $amount,
        string $description,
        string $returnUrl
    ): PaymentGatewayResult {
        try {
            $paymentMethod = $this->createPaymentMethod($creditCard);
            $customer = $this->createCustomer($customerDetails);
            $paymentIntent = $this->createPaymentIntent(
                $paymentMethod,
                $customer,
                $amount,
                $description,
                $returnUrl
            );
        } catch (ApiErrorException $e) {
            return $this->getResultFromApiException($e);
        }

        return $this->getResultFromPaymentIntent($paymentIntent);
    }

    /**
     * @param ApiErrorException $exception
     * @return PaymentGatewayResult
     */
    private function getResultFromApiException(
        ApiErrorException $exception
    ): PaymentGatewayResult {
        $errorBody = $exception->getHttpBody();
        $errorBody = json_decode($errorBody);
        $errorCode = $errorBody->error->code;
        $errorMessage = $this->mapErrorCode($errorCode);

        return new PaymentGatewayResult(false, $errorMessage);
    }

    /**
     * @param PaymentIntent $paymentIntent
     * @return PaymentGatewayResult
     */
    private function getResultFromPaymentIntent(
        PaymentIntent $paymentIntent
    ): PaymentGatewayResult {
        $result = new PaymentGatewayResult();

        if ($paymentIntent->status === self::PAYMENT_INTENT_STATUS_SUCCEEDED) {
            $result->setIsSuccess(true);
        }

        $lastPaymentError = $paymentIntent->last_payment_error;
        if ($lastPaymentError) {
            $errorCode = $lastPaymentError->code;
            $errorMessage = $this->mapErrorCode($errorCode);
            $result->setErrorMessage($errorMessage);
        }

        $nextAction = $paymentIntent->next_action;

        if ($nextAction !== null) {
            $result->setIsFurtherActionRequired(true);

            if ($nextAction->type === self::PAYMENT_INTENT_NEXT_ACTION_TYPE_REDIRECT) {
                $result->setFurtherActionUrl($nextAction->redirect_to_url->url);
            }
        }

        return $result;
    }

    /**
     * @param string $verificationString
     * @return PaymentGatewayResult
     */
    public function verifyCharge(
        string $verificationString
    ): PaymentGatewayResult {
        try {
            $paymentIntent = $this->retrievePaymentIntent($verificationString);
        } catch (ApiErrorException $e) {
            return $this->getResultFromApiException($e);
        }

        return $this->getResultFromPaymentIntent($paymentIntent);
    }
}
