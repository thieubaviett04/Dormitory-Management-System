<?php

namespace App\Console\Commands;

use App\Services\ContractService;
use Illuminate\Console\Command;

class ExpireContracts extends Command
{
    protected $signature = 'contracts:expire';

    protected $description = 'Expire due contracts and release their beds';

    public function handle(ContractService $service): int
    {
        $count = $service->expireDueContracts();

        $this->info("Đã xử lý {$count} hợp đồng hết hạn.");

        return self::SUCCESS;
    }
}
