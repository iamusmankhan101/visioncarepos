<?php

namespace App\Console\Commands;

use App\Transaction;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixImportedSaleDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:fixImportedSaleDates {--delete-bad-rows : Delete imported sales with CSV metadata text (Total:, Invoice No., etc.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix imported sales that have broken transaction dates (year before 2000) or CSV metadata rows';

    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        parent::__construct();
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
    {
        $business_id = request()->session()->get('user.business_id');

        // Step 1: Fix broken dates
        $brokenSales = Transaction::where('type', 'sell')
            ->whereNotNull('import_batch')
            ->whereRaw('YEAR(transaction_date) < 2000')
            ->get();

        $count = $brokenSales->count();

        if ($count > 0) {
            $this->info("Found {$count} imported sales with broken dates.");

            $updated = 0;
            foreach ($brokenSales as $sale) {
                $newDate = $sale->import_time ?? $sale->created_at;

                if ($newDate) {
                    $parsed = Carbon::parse($newDate);
                    if ($parsed->year >= 2000 && $parsed->year <= 2100) {
                        $sale->transaction_date = $parsed->toDateTimeString();
                        $sale->save();
                        $updated++;
                    }
                }
            }

            $this->info("Updated {$updated} transactions with corrected dates.");
        } else {
            $this->info('No imported sales with broken dates found.');
        }

        // Step 2: Delete bad rows if --delete-bad-rows flag is set
        if ($this->option('delete-bad-rows')) {
            $metadata_keywords = [
                'total:', 'total', 'invoice no', 'customer name', 'contact number',
                'invoice no.', 'sub total', 'grand total', 'discount', 'tax',
                'amount', 'shipping', 'payment', 'due', 'balance', 'summary',
            ];

            $badSales = Transaction::where('type', 'sell')
                ->whereNotNull('import_batch')
                ->where(function ($query) use ($metadata_keywords) {
                    foreach ($metadata_keywords as $keyword) {
                        $query->orWhereRaw("LOWER(invoice_no) = ?", [$keyword]);
                        $query->orWhereRaw("LOWER(invoice_no) LIKE ?", ["%{$keyword}%"]);
                    }
                })
                ->get();

            $deleteCount = $badSales->count();

            if ($deleteCount > 0) {
                $this->info("Found {$deleteCount} imported sales with CSV metadata text.");

                $deleted = 0;
                foreach ($badSales as $sale) {
                    try {
                        $this->transactionUtil->deleteSale($sale->business_id, $sale->id);
                        $deleted++;
                    } catch (\Exception $e) {
                        $this->warn("Could not delete sale ID {$sale->id}: " . $e->getMessage());
                    }
                }

                $this->info("Deleted {$deleted} bad sales records.");
            } else {
                $this->info('No imported sales with CSV metadata text found.');
            }
        }

        return 0;
    }
}
