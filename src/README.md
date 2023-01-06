# Laravel Payments

This project is a simple RESTful API that integrates Payment Service Providers (PSPs) into a single, unified interface.
The following PSPs are supported:

- Stripe
- PinPayments

This project is built on:

- PHP 7.3
- Laravel 8
- MySQL 8
- Doctrine ORM (instead of Eloquent)

## Configuring .env files

.env files are provided for both Docker Compose and Laravel, and must be
configured in order to boot up the project.

### For Docker Compose .env

From the repository root, run the following commands:

    cd docker
    cp .env.example .env

You can edit the newly-created `.env` file if you want.

### For Laravel .env

From the repository root, run the following commands:

    cd src
    cp .env.example .env

You should edit the newly-created `.env` file in order to match the Docker Compose
configuration.

## Booting up the project

A docker-compose.yml file is provided for development purposes only and can be used as follows:

    docker compose up -d

As soon as the containers are ready, an empty database named `laravel_payments` will be created.
Connect to the `php` container as follows:

    docker compose exec php bash

In the `php` container, run the following commands in order to install Composer dependencies, generate the
application key, and create the database schema:

    composer install
    php artisan key:generate
    php artisan doctrine:schema:create

In order to create test merchants, run the following command:

    php artisan merchants:init

Please take a note of the command output because it will contain the necessary API tokens of the test merchants.
Since these tokens are stored hashed, you will only get one chance to view them as plaintext.

In order to run the tests, run the following command:

    php artisan test

The API will be accessible at [http://localhost:8080/api](http://localhost:8080/api).

Once you are done using the API, bring down the containers like this:

    docker compose down
