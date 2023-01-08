<?php

namespace Tests\Feature;

use App\Entities\ApiToken;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;
use DateInterval;
use DateTime;

class AuthenticationTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function test_missing_authentication_credentials(): void
    {
        $response = $this->getCreateResponse();

        $this->assertFailedAuthenticationResponse($response);
    }

    /**
     * @return void
     */
    public function test_incorrect_authentication_credentials(): void
    {
        $response = $this->getAuthenticatedCreateResponse(
            [],
            'user',
            'pass'
        );

        $this->assertFailedAuthenticationResponse($response);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    public function test_correct_authentication_credentials(): void
    {
        $merchants = $this->truncateAndCreateTestMerchants();

        $response = $this->getAuthenticatedCreateResponse(
            [],
            $merchants[0]['username'],
            $merchants[0]['apiToken']
        );

        $this->assertAllValidationErrorsPresent($response);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    public function test_expired_authentication_credentials(): void
    {
        $merchants = $this->truncateAndCreateTestMerchants();

        $username = $merchants[0]['username'];
        $apiTokenString = $merchants[0]['apiToken'];
        $apiTokenId = $merchants[0]['apiTokenId'];

        $apiToken = $this->apiTokensService->findById($apiTokenId);

        $this->assertNotNull($apiToken);

        $this->setApiTokenExpired($apiToken);

        $response = $this->getAuthenticatedCreateResponse(
            [],
            $username,
            $apiTokenString,
        );

        $this->assertFailedAuthenticationResponse($response);

        $this->setApiTokenNotExpired($apiToken);

        $response = $this->getAuthenticatedCreateResponse(
            [],
            $username,
            $apiTokenString,
        );

        $this->assertAllValidationErrorsPresent($response);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    public function test_no_api_tokens_exist_for_merchant(): void
    {
        $merchants = $this->truncateAndCreateTestMerchants(false);

        $username = $merchants[0]['username'];

        $response = $this->getAuthenticatedCreateResponse(
            [],
            $username,
            'test-api-token',
        );

        $this->assertFailedAuthenticationResponse($response);
    }

    /**
     * @param ApiToken $apiToken
     * @param DateTime $expiresAt
     * @return void
     */
    private function setApiTokenExpiration(
        ApiToken $apiToken,
        DateTime $expiresAt
    ): void {
        $apiToken->setExpiresAt($expiresAt);
        $this->apiTokensService->save($apiToken);
    }

    /**
     * @param ApiToken $apiToken
     * @return void
     */
    private function setApiTokenExpired(ApiToken $apiToken): void
    {
        $interval = DateInterval::createFromDateString('1 year');
        $expiresAt = (new DateTime())->sub($interval);
        $this->setApiTokenExpiration($apiToken, $expiresAt);
    }

    /**
     * @param ApiToken $apiToken
     * @return void
     */
    private function setApiTokenNotExpired(ApiToken $apiToken): void
    {
        $interval = DateInterval::createFromDateString('1 year');
        $expiresAt = (new DateTime())->add($interval);
        $this->setApiTokenExpiration($apiToken, $expiresAt);
    }
}
