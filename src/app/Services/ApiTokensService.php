<?php

namespace App\Services;

use App\Entities\ApiToken;
use App\Repositories\ApiTokenRepositoryInterface;

class ApiTokensService
{
    /** @var ApiTokenRepositoryInterface $apiTokenRepository */
    private $apiTokenRepository;

    /** @var HashingService $hashingService */
    private $hashingService;

    public function __construct(
        ApiTokenRepositoryInterface $apiTokenRepository,
        HashingService $hashingService
    ) {
        $this->apiTokenRepository = $apiTokenRepository;
        $this->hashingService = $hashingService;
    }

    /**
     * @return ApiToken[]
     */
    public function all(): array
    {
        return $this->apiTokenRepository->all();
    }

    /**
     * @param int $id
     * @return ApiToken|null
     */
    public function findById(int $id): ?ApiToken
    {
        return $this->apiTokenRepository->findById($id);
    }

    /**
     * @param string $token
     * @return ApiToken|null
     */
    public function findByToken(string $token): ?ApiToken
    {
        return $this->apiTokenRepository->findByToken($token);
    }

    /**
     * @param ApiToken $apiToken
     * @return ApiToken
     */
    public function create(ApiToken $apiToken): ApiToken
    {
        if ($apiToken->getId() !== null) {
            return $apiToken;
        }

        $plaintextToken = $apiToken->getToken();

        $hashedToken = $this->hashingService->make($plaintextToken);

        $apiToken->setTokenHash($hashedToken);

        $this->apiTokenRepository->save($apiToken);

        return $apiToken;
    }

    /**
     * @param ApiToken $apiToken
     * @return void
     */
    public function delete(ApiToken $apiToken): void
    {
        $this->apiTokenRepository->delete($apiToken);
    }
}
