<?php

namespace Tests\Feature;

use App\Billing\PaymentGateways\PinPaymentsPaymentGateway;
use App\Billing\PaymentGateways\StripePaymentGateway;
use App\CodeLists\PaymentServiceProviders;
use App\Entities\Merchant;
use App\Exceptions\IllegalArgumentException;
use App\Exceptions\InvalidMerchantPaymentGatewayException;
use App\Services\PaymentGatewayService;

class PaymentGatewayServiceTest extends AbstractTestCase
{
    /**
     * @throws InvalidMerchantPaymentGatewayException
     * @throws IllegalArgumentException
     */
    public function test_returns_correct_payment_gateway_class(): void
    {
        $merchant = new Merchant(
            '',
            '',
            PaymentServiceProviders::STRIPE,
            'test-secret-key'
        );

        $paymentService = PaymentGatewayService::getByMerchant($merchant);

        $this->assertInstanceOf(StripePaymentGateway::class, $paymentService);

        $merchant->setPreferredPaymentService(
            PaymentServiceProviders::PINPAYMENTS
        );

        $paymentService = PaymentGatewayService::getByMerchant($merchant);

        $this->assertInstanceOf(
            PinPaymentsPaymentGateway::class,
            $paymentService
        );

        $this->expectException(IllegalArgumentException::class);

        $merchant->setPreferredPaymentService('');

        PaymentGatewayService::getByMerchant($merchant);

        $this->expectException(InvalidMerchantPaymentGatewayException::class);

        $merchant->setPreferredPaymentService(
            'nonexistent-payment-service'
        );

        PaymentGatewayService::getByMerchant($merchant);
    }
}
