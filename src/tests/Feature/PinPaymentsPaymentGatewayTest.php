<?php

namespace Tests\Feature;

class PinPaymentsPaymentGatewayTest extends AbstractPaymentGatewayTestCase
{
    /** @var string $username */
    protected $username = self::MERCHANT_USERNAME_PINPAYMENTS;

    /** @var string CARD_NUMBER_SUCCESS */
    protected const CARD_NUMBER_SUCCESS = '4200000000000000';

    /** @var string CARD_NUMBER_DECLINED */
    protected const CARD_NUMBER_DECLINED = '4100000000000001';

    /** @var string CARD_NUMBER_3DS */
    protected const CARD_NUMBER_3DS = '4242424242424242';
}
