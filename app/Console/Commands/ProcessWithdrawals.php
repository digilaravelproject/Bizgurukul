<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WalletService;

class ProcessWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically convert commissions from HOLD to AVAILABLE based on holding period.';

    /**
     * Execute the console command.
     */
    public function handle(WalletService $walletService)
    {
        $this->info('Starting automated commission status sync (Hold -> Available)...');
        $walletService->syncAvailableCommissions();
        $this->info('Commission status sync completed.');
    }
}
