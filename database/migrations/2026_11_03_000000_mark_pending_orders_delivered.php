<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('transactions')
            ->whereIn('type', ['sell', 'sales_order', 'purchase_order'])
            ->where(function ($query) {
                $query->whereNull('shipping_status')
                    ->orWhere('shipping_status', '')
                    ->orWhereIn('shipping_status', ['ordered', 'packed']);
            })
            ->update(['shipping_status' => 'delivered']);
    }

    public function down()
    {
    }
};
