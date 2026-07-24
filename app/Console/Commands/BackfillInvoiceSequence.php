<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class BackfillInvoiceSequence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:backfill-invoice-sequence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill sequential invoice numbers for existing successful payment records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting invoice sequence backfill for successful payments...');

        $successfulPayments = Payment::whereIn('status', ['success', 'captured'])
            ->whereNull('invoice_sequence')
            ->orderBy('id', 'asc')
            ->get();

        if ($successfulPayments->isEmpty()) {
            $this->info('No successful payments require backfilling.');
            return 0;
        }

        $currentMax = Payment::whereIn('status', ['success', 'captured'])
            ->whereNotNull('invoice_sequence')
            ->max('invoice_sequence');

        if (is_null($currentMax)) {
            // Start offset at 114 so first successful payment gets sequence 115
            $currentMax = 114;
        }

        $count = 0;
        DB::transaction(function () use ($successfulPayments, &$currentMax, &$count) {
            foreach ($successfulPayments as $payment) {
                $currentMax++;
                // Quietly save to avoid re-triggering booted event if any
                DB::table('payments')
                    ->where('id', $payment->id)
                    ->update(['invoice_sequence' => $currentMax]);
                $count++;
            }
        });

        $this->info("Successfully backfilled {$count} payment records! Next new payment sequence will be " . ($currentMax + 1) . ".");

        return 0;
    }
}
