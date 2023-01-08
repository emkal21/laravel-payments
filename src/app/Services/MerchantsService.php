<?php

namespace App\Services;

use App\Entities\Merchant;
use App\Repositories\MerchantRepositoryInterface;

class MerchantsService
{
    /** @var MerchantRepositoryInterface $merchantRepository */
    private $merchantRepository;

    public function __construct(MerchantRepositoryInterface $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @return Merchant[]
     */
    public function all(): array
    {
        return $this->merchantRepository->all();
    }

    /**
     * @param int $id
     * @return Merchant|null
     */
    public function findById(int $id): ?Merchant
    {
        return $this->merchantRepository->findById($id);
    }

    /**
     * @param string $username
     * @return Merchant|null
     */
    public function findByUsername(string $username): ?Merchant
    {
        return $this->merchantRepository->findByUsername($username);
    }

    /**
     * @param Merchant $merchant
     * @return Merchant
     */
    public function save(Merchant $merchant): Merchant
    {
        return $this->merchantRepository->save($merchant);
    }

    /**
     * @param Merchant $merchant
     * @return void
     */
    public function delete(Merchant $merchant): void
    {
        $this->merchantRepository->delete($merchant);
    }

    /**
     * @return void
     */
    public function truncate(): void
    {
        $this->merchantRepository->truncate();
    }
}
