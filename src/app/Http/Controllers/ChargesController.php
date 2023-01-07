<?php

namespace App\Http\Controllers;

use App\Billing\CreditCard;
use App\Billing\CustomerDetails;
use App\Billing\PaymentGatewayResult;
use App\Exceptions\InvalidMerchantPaymentGatewayException;
use App\Responses\ErrorResponse;
use App\Responses\PaymentGatewayResponse;
use App\Services\PaymentGatewayService;
use App\Validators\Charges\CreateChargeValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChargesController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = new CreateChargeValidator($request->all());

        $errors = $validator->validate();

        if (count($errors) > 0) {
            return (new ErrorResponse($errors))->send();
        }

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

        $expirationDate = $request->input('card.expiration_date');
        $amount = intval($request->input('amount'));
        $description = $request->input('description');

        list($expirationMonth, $expirationYear) =
            CreditCard::splitExpirationDate($expirationDate);

        $creditCard = new CreditCard(
            $request->input('card.card_number'),
            $expirationMonth,
            $expirationYear,
            $request->input('card.cvv'),
            $request->input('card.cardholder_name')
        );

        $customerDetails = new CustomerDetails(
            $request->input('customer.email'),
            $request->input('customer.address_line_1'),
            $request->input('customer.address_city'),
            $request->input('customer.address_country')
        );

        $returnUrl = PaymentGatewayService::getReturnUrlByMerchant($this->merchant);

        $result = $paymentGateway->createCharge(
            $creditCard,
            $customerDetails,
            $amount,
            $description,
            $returnUrl
        );

        return (new PaymentGatewayResponse($result))->send();
    }
}
