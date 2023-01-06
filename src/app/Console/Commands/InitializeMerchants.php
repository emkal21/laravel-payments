<?php

namespace App\Console\Commands;

use App\Entities\ApiToken;
use App\Entities\Merchant;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;
use App\Services\ApiTokensService;
use App\Services\MerchantsService;
use DateInterval;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Ramsey\Uuid\Uuid;

class InitializeMerchants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchants:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates test merchants and API tokens.';

    /** @var MerchantsService $merchantsService */
    private $merchantsService;

    /** @var ApiTokensService $apiTokensService */
    private $apiTokensService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        MerchantsService $merchantsService,
        ApiTokensService $apiTokensService
    ) {
        parent::__construct();

        $this->merchantsService = $merchantsService;
        $this->apiTokensService = $apiTokensService;
    }

    /**
     * @return array
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     */
    private function getMerchantsFromFile(): array
    {
        $merchantsFilePath = base_path('test-data/merchants.json');

        if (!file_exists($merchantsFilePath)) {
            throw new FileNotFoundException('Merchants file cannot be found.');
        }

        $merchantsData = file_get_contents($merchantsFilePath);

        $merchants = json_decode($merchantsData, true);

        if ($merchants === null) {
            throw new FileUnreadableException('Merchants file cannot be read.');
        }

        return $merchants;
    }

    /**
     * @param array $data
     * @return Merchant
     * @throws IllegalArgumentException
     */
    private function createMerchant(array $data): Merchant
    {
        $name = Arr::get(
            $data,
            'name',
            ''
        );

        $preferredPaymentService = Arr::get(
            $data,
            'preferredPaymentService',
            ''
        );

        $paymentServiceSecretKey = Arr::get(
            $data,
            'paymentServiceSecretKey',
            ''
        );

        $merchant = new Merchant(
            $name,
            $preferredPaymentService,
            $paymentServiceSecretKey
        );

        $this->merchantsService->save($merchant);

        return $merchant;
    }

    private function createApiToken(Merchant $merchant): ApiToken
    {
        $token = (string)Uuid::uuid4();

        $interval = DateInterval::createFromDateString('1 year');

        $expiresAt = (new DateTime())->add($interval);

        $apiToken = new ApiToken($merchant->getId(), $token, $expiresAt);

        $this->apiTokensService->create($apiToken);

        return $apiToken;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws FileNotFoundException
     * @throws FileUnreadableException
     * @throws IllegalArgumentException
     */
    public function handle(): int
    {
        $merchants = $this->getMerchantsFromFile();

        $merchantCredentials = [];
        $nameHeading = 'name';
        $apiTokenHeading = 'apiToken';
        $tableHeadings = [$nameHeading, $apiTokenHeading];

        foreach ($merchants as $merchantData) {
            $merchant = $this->createMerchant($merchantData);
            $apiToken = $this->createApiToken($merchant);

            $merchantCredentials[] = [
                $nameHeading => $merchant->getName(),
                $apiTokenHeading => $apiToken->getToken(),
            ];
        }

        $this->info('Test merchants have been successfully created!');

        $this->table($tableHeadings, $merchantCredentials);

        return 0;
    }
}
