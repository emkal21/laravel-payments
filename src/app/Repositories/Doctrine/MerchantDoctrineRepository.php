<?php

namespace App\Repositories\Doctrine;

use App\Entities\Merchant;
use App\Repositories\MerchantRepositoryInterface;

class MerchantDoctrineRepository extends AbstractDoctrineRepository implements MerchantRepositoryInterface
{
    /**
     * @return class-string<Merchant>
     */
    protected function getEntityClass(): string
    {
        return Merchant::class;
    }

    /**
     * @param string $username
     * @return Merchant|null
     */
    public function findByUsername(string $username): ?Merchant
    {
        return $this->findByField('username', $username);
    }
}
