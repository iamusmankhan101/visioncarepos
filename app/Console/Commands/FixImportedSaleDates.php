<?php

namespace App\Console\Commands;

use App\Transaction;
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
    protected $signature = 'pos:fixImportedSaleDates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix imported sales that have broken transaction dates (year before 2000)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
    {
        // Find all imported transactions with dates before year 2000
        $brokenSales = Transaction::where('type', 'sell')
            ->whereNotNull('import_batch')
            ->whereRaw('YEAR(transaction_date) < 2000')
            ->get();

        $count = $brokenSales->count();

        if ($count === 0) {
            $this->info('No imported sales with broken dates found.');
            return 0;
        }

        $this->info("Found {$count} imported sales with broken dates.");

        // Update each transaction's date to the import_time (when it was imported)
        // if available, otherwise to the import batch's earliest created_at
        $updated = 0;
        foreach ($brokenSales as $sale) {
            // Use import_time if available, otherwise use the transaction's created_at
            $newDate = $sale->import_time ?? $sale->created_at;

            if ($newDate) {
                // Make sure the new date is reasonable
                $parsed = Carbon::parse($newDate);
                if ($parsed->year >= 2000 && $parsed->year <= 2100) {
                    $sale->transaction_date = $parsed->toDateTimeString();
                    $sale->save();
                    $updated++;
                }
            }
        }

        $this->info("Updated {$updated} transactions with corrected dates.");

        return 0;
    }
}
