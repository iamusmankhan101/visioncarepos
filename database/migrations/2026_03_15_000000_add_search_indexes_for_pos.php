<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->index('mobile');
            $table->index('name');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('sku');
        });

        // Variations sub_sku is already indexed in some migrations but ensuring it here for consistency
        Schema::table('variations', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('variations');
            if (!array_key_exists('variations_sub_sku_index', $indexes)) {
                $table->index('sub_sku');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['mobile']);
            $table->dropIndex(['name']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['sku']);
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->dropIndex(['sub_sku']);
        });
    }
};
