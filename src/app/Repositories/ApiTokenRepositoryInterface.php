<?php

namespace App\Repositories;

use App\Entities\ApiToken;

/**
 * @extends RepositoryInterface<ApiToken>
 */
interface ApiTokenRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $token
     * @return ApiToken|null
     */
    public function findByToken(string $token): ?ApiToken;
}
