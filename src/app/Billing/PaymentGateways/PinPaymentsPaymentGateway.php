<?php

namespace App\Billing\PaymentGateways;

use App\Billing\CreditCard;
use App\Billing\CustomerDetails;
use App\Billing\PaymentGatewayResult;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class PinPaymentsPaymentGateway extends AbstractPaymentGateway
{
    /** @var string $verificationStringQueryParam */
    protected $verificationStringQueryParam = 'session_token';

    /** @var Client $httpClient */
    private $httpClient;

    /** @var string LIVE_BASE_URI */
    private const LIVE_BASE_URI = 'https://api.pinpayments.com/1/';

    /** @var string TEST_BASE_URI */
    private const TEST_BASE_URI = 'https://test-api.pinpayments.com/1/';

    /** @var array CHARGE_ERROR_CODES */
    protected const CHARGE_ERROR_CODES = [
        'card_declined' => self::CHARGE_GENERIC_ERROR,
        'insufficient_funds' =>
            'The provided payment method has insufficient funds.',
        'processing_error' =>
            'An error occurred while processing the provided card.',
        'suspected_fraud' =>
            'The transaction was flagged as possibly fraudulent and ' .
            'subsequently declined.',
        'expired_card' =>
            'The provided payment method has expired.',
        'lost_card' =>
            'The provided payment method has been reported lost ' .
            'by the card issuer.',
        'stolen_card' =>
            'The provided payment method has been reported stolen ' .
            'by the card issuer.',
        'invalid_cvv' =>
            'The provided CVV was not in the correct format.',
        'invalid_card' =>
            'The provided card was invalid.',
        'gateway_error' =>
            'The payment service provider encountered an error while ' .
            'processing the transaction. Please try again.',
        'unknown' =>
            'An unknown error has occurred.',
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

        $baseUri = $this->getBaseUri();

        $this->httpClient = new Client(['base_uri' => $baseUri]);
    }

    /**
     * @return string
     */
    private function getBaseUri(): string
    {
        return $this->isTest
            ? self::TEST_BASE_URI
            : self::LIVE_BASE_URI;
    }

    /**
     * @return array
     */
    private function getCredentials(): array
    {
        return [$this->secretKey, ''];
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function makeRequest(
        string $method,
        string $endpoint,
        array $data = []
    ): ResponseInterface {
        $credentials = ['auth' => $this->getCredentials()];

        $params = array_merge($credentials, $data);

        return $this->httpClient->request($method, $endpoint, $params);
    }

    /**
     * @param string $endpoint
     * @param array $queryData
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function makeGetRequest(
        string $endpoint,
        array $queryData = []
    ): ResponseInterface {
        return $this->makeRequest('GET', $endpoint, [
            'query' => $queryData
        ]);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function makePostRequest(
        string $endpoint,
        array $data = []
    ): ResponseInterface {
        return $this->makeRequest('POST', $endpoint, [
            'json' => $data
        ]);
    }

    /**
     * @param CreditCard $creditCard
     * @param CustomerDetails $customerDetails
     * @param int $amount
     * @param string $description
     * @param string $returnUrl
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function postCharge(
        CreditCard $creditCard,
        CustomerDetails $customerDetails,
        int $amount,
        string $description,
        string $returnUrl
    ): ResponseInterface {
        return $this->makePostRequest('charges', [
            'amount' => $amount,
            'currency' => $this->getDefaultCurrency(),
            'email' => $customerDetails->getEmail(),
            'description' => $description,
            'card' => [
                'number' => $creditCard->getCardNumber(),
                'expiry_month' => $creditCard->getExpirationMonth(),
                'expiry_year' => $creditCard->getExpirationYear(),
                'cvc' => $creditCard->getCvv(),
                'name' => $creditCard->getCardholderName(),
                'address_line1' => $customerDetails->getAddressLine1(),
                'address_city' => $customerDetails->getAddressCity(),
                'address_country' => $customerDetails->getAddressCountry(),
            ],
            'three_d_secure' => [
                'enabled' => true,
                'fallback_ok' => true,
                'callback_url' => $returnUrl,
            ],
        ]);
    }

    /**
     * @param string $sessionToken
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function getChargeVerify(
        string $sessionToken
    ): ResponseInterface {
        return $this->makeGetRequest('charges/verify', [
            'session_token' => $sessionToken
        ]);
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
            $response = $this->postCharge(
                $creditCard,
                $customerDetails,
                $amount,
                $description,
                $returnUrl
            );
        } catch (ClientException $e) {
            return $this->getResultFromExceptionResponse($e);
        } catch (GuzzleException|Exception $e) {
            return $this->getGenericErrorResult();
        }

        return $this->getResultFromResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return PaymentGatewayResult
     */
    private function getResultFromResponse(
        ResponseInterface $response
    ): PaymentGatewayResult {
        $responseString = (string)$response->getBody();

        $responseObject = json_decode($responseString);

        if (property_exists($responseObject, 'response')) {
            $responseObject = $responseObject->response;
        }

        $result = new PaymentGatewayResult();

        switch ($response->getStatusCode()) {
            case 200:
            case 201:
                if ($responseObject->success === true) {
                    $result->setIsSuccess(true);
                }
                break;
            case 202:
                $result->setIsFurtherActionRequired(true);
                $result->setFurtherActionUrl($responseObject->redirect_url);
                break;
        }

        $errorMessage = property_exists($responseObject, 'error_message')
            ? $responseObject->error_message
            : null;

        if ($errorMessage !== null) {
            $result->setErrorMessage($errorMessage);
        }

        return $result;
    }

    /**
     * @param ClientException $exception
     * @return PaymentGatewayResult
     */
    private function getResultFromExceptionResponse(
        ClientException $exception
    ): PaymentGatewayResult {
        $response = $exception->getResponse();

        $responseString = (string)$response->getBody();

        $responseObject = json_decode($responseString);

        $result = new PaymentGatewayResult();

        $result->setIsSuccess(false);

        $errorCode = property_exists($responseObject, 'error')
            ? $responseObject->error
            : null;

        if ($errorCode !== null) {
            $errorMessage = $this->mapErrorCode($errorCode);
            $result->setErrorMessage($errorMessage);
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
            $response = $this->getChargeVerify($verificationString);
        } catch (ClientException $e) {
            return $this->getResultFromExceptionResponse($e);
        } catch (GuzzleException $e) {
            return $this->getGenericErrorResult();
        }

        return $this->getResultFromResponse($response);
    }
}
