<?php

namespace App\Repositories;

use App\Entities\ApiToken;

/**
 * @extends RepositoryInterface<ApiToken>
 */
interface ApiTokenRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $merchantId
     * @return ApiToken[]
     */
    public function getByMerchantId(int $merchantId): array;
}
