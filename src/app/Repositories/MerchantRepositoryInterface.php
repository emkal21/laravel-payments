<?php

namespace App\Repositories;

use App\Entities\Merchant;

/**
 * @extends RepositoryInterface<Merchant>
 */
interface MerchantRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $username
     * @return Merchant|null
     */
    public function findByUsername(string $username): ?Merchant;
}
