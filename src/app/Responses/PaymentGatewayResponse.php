<?php

namespace App\Responses;

use App\Billing\PaymentGatewayResult;

class PaymentGatewayResponse extends BaseResponse
{
    /* @var PaymentGatewayResult $paymentGatewayResult */
    protected $paymentGatewayResult;

    /**
     * @param PaymentGatewayResult $paymentGatewayResult
     */
    public function __construct(PaymentGatewayResult $paymentGatewayResult)
    {
        $this->paymentGatewayResult = $paymentGatewayResult;
    }

    protected function getPayload(): array
    {
        return $this->paymentGatewayResult->toArray();
    }

    /**
     * @return int
     */
    protected function getHttpStatus(): int
    {
        if ($this->paymentGatewayResult->isSuccess()) {
            return 200;
        }

        if ($this->paymentGatewayResult->isFurtherActionRequired()) {
            return 202;
        }

        return 400;
    }
}
