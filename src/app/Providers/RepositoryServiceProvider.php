<?php

namespace App\Providers;

use App\Repositories\ApiTokenRepositoryInterface;
use App\Repositories\Doctrine\ApiTokenDoctrineRepository;
use App\Repositories\Doctrine\MerchantDoctrineRepository;
use App\Repositories\MerchantRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->bind(
            MerchantRepositoryInterface::class,
            MerchantDoctrineRepository::class
        );

        $this->app->bind(
            ApiTokenRepositoryInterface::class,
            ApiTokenDoctrineRepository::class
        );
    }
}
