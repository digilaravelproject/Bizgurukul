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
    protected $description = 'Set legacy (id + 114) for pre-July 2026 payments and continuous sequence from 1555 for July 1, 2026 onwards';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting invoice sequence alignment...');

        // Step 1: Set legacy invoice_sequence (id + 114) for all payments created before July 1, 2026
        // This ensures 30 June 2026 last bill number matches client historical record (1554)
        $preJulyPayments = Payment::where('created_at', '<', '2026-07-01 00:00:00')->get();
        $legacyCount = 0;
        foreach ($preJulyPayments as $p) {
            $legacySeq = $p->id + 114;
            if ($p->invoice_sequence !== $legacySeq) {
                DB::table('payments')->where('id', $p->id)->update(['invoice_sequence' => $legacySeq]);
                $legacyCount++;
            }
        }
        $this->info("Aligned {$legacyCount} pre-July 2026 historical payments to legacy formula (id + 114).");

        // Step 2: Set continuous sequential numbering starting at 1555 for successful July 1, 2026+ payments
        $julyPayments = Payment::whereIn('status', ['success', 'captured'])
            ->where('created_at', '>=', '2026-07-01 00:00:00')
            ->orderBy('id', 'asc')
            ->get();

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

        $this->info("Aligned {$julyCount} July 2026 payments starting at sequence 1555!");
        $this->info("Next new payment sequence will be {$seq}.");

        return 0;
    }
}
