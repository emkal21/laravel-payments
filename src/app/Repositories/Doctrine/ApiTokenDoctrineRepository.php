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
     * @param string $token
     * @return ApiToken|null
     */
    public function findByToken(string $token): ?ApiToken
    {
        return $this->findByField('token', $token);
    }
}
