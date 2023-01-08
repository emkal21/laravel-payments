<?php

namespace Tests\Feature;

class StripePaymentGatewayTest extends AbstractPaymentGatewayTestCase
{
    /** @var string $username */
    protected $username = self::MERCHANT_USERNAME_STRIPE;

    /** @var string CARD_NUMBER_SUCCESS */
    protected const CARD_NUMBER_SUCCESS = '4242424242424242';

    /** @var string CARD_NUMBER_DECLINED */
    protected const CARD_NUMBER_DECLINED = '4000000000000002';

    /** @var string CARD_NUMBER_3DS */
    protected const CARD_NUMBER_3DS = '4000002760003184';
}
