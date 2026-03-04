<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignedCustomerToTransactionSellLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->integer('assigned_customer_id')->unsigned()->nullable()->after('res_service_staff_id');
            $table->foreign('assigned_customer_id')->references('id')->on('contacts')->onDelete('set null');
            
            $table->index('assigned_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropForeign(['assigned_customer_id']);
            $table->dropColumn('assigned_customer_id');
        });
    }
}
