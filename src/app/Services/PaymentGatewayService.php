<?php

namespace App\Services;

use App\Billing\PaymentGateways\AbstractPaymentGateway;
use App\Billing\PaymentGateways\PinPaymentsPaymentGateway;
use App\Billing\PaymentGateways\StripePaymentGateway;
use App\CodeLists\PaymentServiceProviders;
use App\Entities\Merchant;
use App\Exceptions\InvalidMerchantPaymentGatewayException;

class PaymentGatewayService
{
    /**
     * @param Merchant $merchant
     * @param bool $isTest
     * @return AbstractPaymentGateway
     * @throws InvalidMerchantPaymentGatewayException
     */
    public static function getByMerchant(
        Merchant $merchant,
        bool $isTest = true
    ): AbstractPaymentGateway {
        $preferredPaymentService = $merchant->getPreferredPaymentService();
        $paymentServiceSecretKey = $merchant->getPaymentServiceSecretKey();

        switch ($preferredPaymentService) {
            case PaymentServiceProviders::STRIPE:
                return new StripePaymentGateway(
                    $paymentServiceSecretKey,
                    $isTest
                );
            case PaymentServiceProviders::PINPAYMENTS:
                return new PinPaymentsPaymentGateway(
                    $paymentServiceSecretKey,
                    $isTest
                );
        }

        throw new InvalidMerchantPaymentGatewayException();
    }

    /**
     * @param Merchant $merchant
     * @return string
     */
    public static function getReturnUrlByMerchant(Merchant $merchant): string
    {
        return route('callbacks.verify', [
            'merchantId' => $merchant->getId()
        ]);
    }
}
