<?php

namespace App\Repositories\Doctrine;

use App\Entities\ApiToken;
use App\Repositories\ApiTokenRepositoryInterface;

class ApiTokenDoctrineRepository extends AbstractDoctrineRepository implements ApiTokenRepositoryInterface
{
    /**
     * @return class-string<ApiToken>
     */
    protected function getEntityClass(): string
    {
        return ApiToken::class;
    }

    /**
     * @param int $merchantId
     * @return ApiToken[]
     */
    public function getByMerchantId(int $merchantId): array
    {
        return $this->getByField('merchantId', $merchantId);
    }
}
