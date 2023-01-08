<?php

namespace App\Services;

use App\Entities\ApiToken;
use App\Entities\Merchant;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;
use DateInterval;
use DateTime;
use Illuminate\Support\Arr;
use Ramsey\Uuid\Uuid;

class MerchantsImportService
{
    /** @var MerchantsService $merchantsService */
    private $merchantsService;

    /** @var ApiTokensService $apiTokensService */
    private $apiTokensService;

    /**
     * @param MerchantsService $merchantsService
     * @param ApiTokensService $apiTokensService
     */
    public function __construct(
        MerchantsService $merchantsService,
        ApiTokensService $apiTokensService
    ) {
        $this->merchantsService = $merchantsService;
        $this->apiTokensService = $apiTokensService;
    }

    /**
     * @param string $path Relative to project's 'src' directory.
     * @return array
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     */
    private function getMerchantsFromFile(string $path): array
    {
        $merchantsFilePath = base_path($path);

        if (!file_exists($merchantsFilePath)) {
            throw new FileNotFoundException('Merchants file cannot be found.');
        }

        $merchantsData = file_get_contents($merchantsFilePath);

        $merchants = json_decode($merchantsData, true);

        if ($merchants === null) {
            throw new FileUnreadableException('Merchants file cannot be read.');
        }

        return $merchants;
    }

    /**
     * @param array $data
     * @return Merchant
     * @throws IllegalArgumentException
     */
    private function createMerchant(array $data): Merchant
    {
        $name = Arr::get(
            $data,
            'name',
            ''
        );

        $username = Arr::get(
            $data,
            'username',
            ''
        );

        $preferredPaymentService = Arr::get(
            $data,
            'preferredPaymentService',
            ''
        );

        $paymentServiceSecretKey = Arr::get(
            $data,
            'paymentServiceSecretKey',
            ''
        );

        $merchant = new Merchant(
            $name,
            $username,
            $preferredPaymentService,
            $paymentServiceSecretKey
        );

        $this->merchantsService->save($merchant);

        return $merchant;
    }

    private function createApiToken(
        Merchant $merchant,
        string $apiTokenString
    ): ApiToken {
        $interval = DateInterval::createFromDateString('1 year');

        $expiresAt = (new DateTime())->add($interval);

        $apiToken = new ApiToken(
            $merchant->getId(),
            $apiTokenString,
            $expiresAt
        );

        $this->apiTokensService->create($apiToken);

        return $apiToken;
    }

    /**
     * @param string $path Relative to project's 'src' directory.
     * @param bool $createApiTokens
     * @return array
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    public function importFromFile(
        string $path,
        bool $createApiTokens = true
    ): array {
        $merchants = $this->getMerchantsFromFile($path);

        $merchantCredentials = [];

        foreach ($merchants as $merchantData) {
            $merchant = $this->createMerchant($merchantData);

            $apiToken = $createApiTokens
                ? $this->createApiToken($merchant, $merchantData['apiToken'])
                : null;

            $merchantCredentials[] = [
                'merchantId' => $merchant->getId(),
                'name' => $merchant->getName(),
                'paymentGateway' => $merchant->getPreferredPaymentService(),
                'username' => $merchant->getUsername(),
                'apiTokenId' => $apiToken === null
                    ? null : $apiToken->getId(),
                'apiToken' => $apiToken === null
                    ? null : $apiToken->getToken(),
            ];
        }

        return $merchantCredentials;
    }
}
