<?php

namespace App\Http\Middleware;

use App\Responses\NotAuthenticatedResponse;
use App\Services\ApiTokensService;
use App\Services\HashingService;
use App\Services\MerchantsService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateUsingBasicAuth
{
    /** @var MerchantsService $merchantsService */
    private $merchantsService;

    /** @var ApiTokensService $apiTokensService */
    private $apiTokensService;

    /** @var HashingService $hashingService */
    private $hashingService;

    public function __construct(
        MerchantsService $merchantsService,
        ApiTokensService $apiTokensService,
        HashingService $hashingService
    ) {
        $this->merchantsService = $merchantsService;
        $this->apiTokensService = $apiTokensService;
        $this->hashingService = $hashingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $username = $request->header('php-auth-user');
        $password = $request->header('php-auth-pw');

        if (empty($username) || empty($password)) {
            return (new NotAuthenticatedResponse())->send();
        }

        $merchant = $this->merchantsService->findByUsername($username);

        if ($merchant === null) {
            return (new NotAuthenticatedResponse())->send();
        }

        $merchantId = $merchant->getId();
        $apiTokens = $this->apiTokensService->getByMerchantId($merchantId);

        foreach ($apiTokens as $apiToken) {
            $isVerified = $this->hashingService->verify(
                $password,
                $apiToken->getTokenHash()
            );

            if ($isVerified && !$apiToken->isExpired()) {
                $request->attributes->add(['merchantId' => $merchantId]);

                return $next($request);
            }
        }

        return (new NotAuthenticatedResponse())->send();
    }
}
