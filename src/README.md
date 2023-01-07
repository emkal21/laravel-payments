# Laravel Payments

This project is a simple RESTful API that integrates Payment Service Providers into a single, unified interface. The
following Payment Service Providers are supported:

- Stripe
- PinPayments

This project is built on:

- PHP 7.3
- Laravel 8
- MySQL 8
- Doctrine ORM (instead of Eloquent)

## Configuring .env files

.env files are provided for both Docker Compose and Laravel, and must be configured in order to boot up the project.

### For Docker Compose .env

From the repository root, run the following commands:

    cd docker
    cp .env.example .env

You can edit the newly-created `.env` file if you want.

### For Laravel .env

From the repository root, run the following commands:

    cd src
    cp .env.example .env

You should edit the newly-created `.env` file in order to match the Docker Compose configuration.

## Booting up the project

A docker-compose.yml file is provided for development purposes only and can be used as follows:

    docker compose up -d

As soon as the containers are ready, an empty database named `laravel_payments` will be created. Connect to the `php`
container as follows:

    docker compose exec php bash

In the `php` container, run the following commands in order to install Composer dependencies, generate the application
key, and create the database schema:

    composer install
    php artisan key:generate
    php artisan doctrine:schema:create

In order to create test merchants, run the following command:

    php artisan merchants:init

Please take a note of the command output because it will contain the necessary API tokens of the test merchants. Since
these tokens are stored hashed, you will only get one chance to view them as plaintext.

In order to run the tests, run the following command:

    php artisan test

The API will be accessible at [http://localhost:8080/api](http://localhost:8080/api).

Once you are done using the API, bring down the containers like this:

    docker compose down

## Test cards

To test the API's functionality, use the card numbers below:

| Expected result        | Stripe           | PinPayments      |
|------------------------|------------------|------------------|
| Successful response    | 4242424242424242 | 4200000000000000 |
| Card declined          | 4000000000000002 | 4100000000000001 |
| 3D Secure flow         | 4000002760003184 | 4242424242424242 |
| 3D Secure flow failure | 4000008260003178 | 4532776623785148 |

- CVV: Use any three-digit number (example: 123)
- Expiration date: Use any future date (example: 12/2024)
- Cardholder name: Use any full name (example: John Doe)

## API endpoints

### Authentication

HTTP Basic Auth is used in order to authenticate the merchant.

- **Username:** The merchant's username
- **Password:** The merchant's apiToken

### POST /charges

Use this endpoint in order to charge a customer's card.

Example payload:

    {
        "card": {
            "card_number": "4242424242424242",
            "expiration_date": "12/2024",
            "cvv": "123",
            "cardholder_name": "John Doe"
        },
        "customer": {
            "email": "email@email.com",
            "address_line_1": "Test address",
            "address_city": "Test city",
            "address_country": "GR"
        },
        "amount": 100,
        "description": "Test description"
    }

| Parameter                | Type    | Required | Remarks                                                                                                              |
|--------------------------|---------|----------|----------------------------------------------------------------------------------------------------------------------|
| card.card_number         | string  | Yes      |                                                                                                                      |
| card.expiration_date     | string  | Yes      | Must be in the mm/yyyy format.                                                                                       |
| card.cvv                 | string  | Yes      |                                                                                                                      |
| card.cardholder_name     | string  | Yes      |                                                                                                                      |
| customer.email           | string  | Yes      |                                                                                                                      |
| customer.address_line_1  | string  | Yes      |                                                                                                                      |
| customer.address_city    | string  | Yes      |                                                                                                                      |
| customer.address_country | string  | Yes      | Must be a 2-letter country code according to [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2). |
| amount                   | integer | Yes      | Must be in EUR cents (example: type 100 for 1.00 EUR).                                                               |
| description              | string  | Yes      |                                                                                                                      |

The following HTTP status codes may occur when using the endpoint above:

| HTTP status code         | Explanation                                                                                      |
|--------------------------|--------------------------------------------------------------------------------------------------|
| 200 OK                   | The API call has completed successfully, and a charge has been made.                             |
| 202 Accepted             | The API call has been received, but further action is necessary in order to finalize the charge. |
| 400 Bad Request          | Charge was unsuccessful due to payment method related errors.                                    |
| 401 Unauthorized         | Authentication information missing or incorrect.                                                 |
| 422 Unprocessable Entity | Malformed or incorrect input.                                                                    |

Example successful response without 3DS (HTTP status code: 200):

    {
        "isSuccess": true,
        "errorMessage": null,
        "isFurtherActionRequired": false,
        "furtherActionUrl": null
    }

Example successful response with 3DS (HTTP status code: 202):

    {
        "isSuccess": false,
        "errorMessage": null,
        "isFurtherActionRequired": true,
        "furtherActionUrl": "https://hooks.stripe.com/redirect/authenticate/..."
    }

Example unsuccessful response (HTTP status code: 400):

    {
        "isSuccess": false,
        "errorMessage": "The provided payment method has been declined.",
        "isFurtherActionRequired": false,
        "furtherActionUrl": null
    }

Example unauthorized response (HTTP status code: 401):

    {
        "errors": [
            "Authentication is required to access this resource."
        ]
    }

Example response with validation errors (HTTP status code: 422):

    {
        "errors": [
            "The card.card number field is required.",
            "The card.expiration date field is required."
        ]
    }

## A note on 3DS

In case the payment method requires 3DS verification, the API returns the appropriate information so that the user can
be redirected, in order to complete the verification challenge, as described above. After verification has completed,
the user is redirected back to a special endpoint of the API (`GET /callbacks/{merchantId}`), in which the final status
of the charge is displayed.

Example successful response (HTTP status code: 200):

    {
        "isSuccess": true,
        "errorMessage": null,
        "isFurtherActionRequired": false,
        "furtherActionUrl": null
    }

Example unsuccessful response (HTTP status code: 400):

    {
        "isSuccess":false,
        "errorMessage":"The provided payment method has been declined.",
        "isFurtherActionRequired":false,
        "furtherActionUrl":null
    }
