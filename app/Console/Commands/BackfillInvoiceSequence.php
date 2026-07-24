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
    protected $signature = 'payments:backfill-invoice-sequence {--force : Force recalculation of July 2026 invoice numbers starting at 1555}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill sequential invoice numbers starting at 1555 from July 1, 2026';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting invoice sequence assignment...');

        // 1. Assign legacy invoice sequence (id + 114) for successful payments before July 1, 2026
        $legacyPayments = Payment::whereIn('status', ['success', 'captured'])
            ->where('created_at', '<', '2026-07-01 00:00:00')
            ->get();

        $legacyCount = 0;
        foreach ($legacyPayments as $p) {
            $seq = $p->id + 114;
            if ($p->invoice_sequence !== $seq) {
                DB::table('payments')->where('id', $p->id)->update(['invoice_sequence' => $seq]);
                $legacyCount++;
            }
        }
        $this->info("Updated {$legacyCount} legacy payments prior to July 1, 2026.");

        // 2. Assign sequential numbers starting at 1555 for payments on or after July 1, 2026
        $julyPaymentsQuery = Payment::whereIn('status', ['success', 'captured'])
            ->where('created_at', '>=', '2026-07-01 00:00:00');

        if (!$this->option('force')) {
            $julyPaymentsQuery->whereNull('invoice_sequence');
        }

        $julyPayments = $julyPaymentsQuery->orderBy('id', 'asc')->get();

        if ($julyPayments->isEmpty()) {
            $this->info('No July 2026 payments require sequence assignment.');
            return 0;
        }

        // Start July sequence at 1555 (since June 30 last bill was 1554)
        $seq = 1555;
        $julyCount = 0;

        DB::transaction(function () use ($julyPayments, &$seq, &$julyCount) {
            foreach ($julyPayments as $payment) {
                DB::table('payments')
                    ->where('id', $payment->id)
                    ->update(['invoice_sequence' => $seq]);
                $seq++;
                $julyCount++;
            }
        });

        $this->info("Successfully assigned sequential invoice numbers for {$julyCount} July 2026 payments starting at 1555!");
        $this->info("Next new payment sequence will be {$seq}.");

        return 0;
    }
}
