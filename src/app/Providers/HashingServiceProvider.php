<?php

namespace App\Providers;

use App\Extensions\Hashing\DefaultHashingDriver;
use App\Extensions\Hashing\HashingDriverInterface;
use Illuminate\Support\ServiceProvider;

class HashingServiceProvider extends ServiceProvider
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
            HashingDriverInterface::class,
            DefaultHashingDriver::class
        );
    }
}
