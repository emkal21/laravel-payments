<?php

namespace App\Console\Commands;

use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUnreadableException;
use App\Exceptions\IllegalArgumentException;
use App\Services\MerchantsImportService;
use Illuminate\Console\Command;

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

    /** @var MerchantsImportService $merchantsImportService */
    private $merchantsImportService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        MerchantsImportService $merchantsImportService
    ) {
        parent::__construct();

        $this->merchantsImportService = $merchantsImportService;
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
        $path = 'test-data/merchants.json';

        $merchantCredentials = $this
            ->merchantsImportService
            ->importFromFile($path);

        $tableHeadings = [
            'merchantId',
            'name',
            'paymentGateway',
            'username',
            'apiTokenId',
            'apiToken',
        ];

        $this->info('Test merchants have been successfully created!');

        $this->table($tableHeadings, $merchantCredentials);

        return 0;
    }
}
