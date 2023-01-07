<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGatewayResult;
use App\Exceptions\InvalidMerchantPaymentGatewayException;
use App\Responses\PaymentGatewayResponse;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallbacksController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $isTestEnvironment = $this->isTestEnvironment();

        try {
            $paymentGateway = PaymentGatewayService::getByMerchant(
                $this->merchant,
                $isTestEnvironment
            );
        } catch (InvalidMerchantPaymentGatewayException $e) {
            $message = 'Current merchant does not have an associated payment service.';

            $result = new PaymentGatewayResult(
                false,
                $message,
                false,
                null
            );

            return (new PaymentGatewayResponse($result))->send();
        }

        $verificationStringParam = $paymentGateway->getVerificationStringQueryParam();

        $verificationString = $request->query($verificationStringParam);

        $result = $paymentGateway->verifyCharge($verificationString);

        return (new PaymentGatewayResponse($result))->send();
    }
}
