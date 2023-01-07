<?php

namespace App\Http\Controllers;

use App\Entities\Merchant;
use App\Services\MerchantsService;
use Closure;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ValidatesRequests;

    /** @var Merchant $merchant */
    protected $merchant;

    /** @var MerchantsService $merchantsService */
    private $merchantsService;

    public function __construct(MerchantsService $merchantsService)
    {
        $this->merchantsService = $merchantsService;

        $this->middleware(function (Request $request, Closure $next) {
            $merchantId = $request->route('merchantId');

            if ($merchantId === null) {
                $merchantId = $request->attributes->get('merchantId');
            }

            $merchantId = intval($merchantId);

            $this->merchant = $this->merchantsService->findById($merchantId);

            return $next($request);
        });
    }

    protected function isTestEnvironment(): bool
    {
        return (bool)config('app.is_test', true);
    }
}
