<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionTargetFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Commission target fields
            $table->enum('target_type', ['none', 'monthly', 'quarterly', 'yearly'])->default('none')->after('condition');
            $table->decimal('target_amount', 22, 4)->nullable()->after('target_type');
            $table->enum('commission_applies_when', ['always', 'target_met', 'target_exceeded'])->default('always')->after('target_amount');
            $table->decimal('bonus_percent', 5, 2)->nullable()->after('commission_applies_when');
            $table->date('target_reset_date')->nullable()->after('bonus_percent');
            $table->text('commission_notes')->nullable()->after('target_reset_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'target_type',
                'target_amount', 
                'commission_applies_when',
                'bonus_percent',
                'target_reset_date',
                'commission_notes'
            ]);
        });
    }
}