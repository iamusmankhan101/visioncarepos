<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Checks for an existing index without needing doctrine/dbal, which this
     * project does not ship.
     *
     * @param  string  $table
     * @param  string  $index
     * @return bool
     */
    private function hasIndex($table, $index)
    {
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //These indexes are also added by hand on some installs, so add only what is missing.
        Schema::table('contacts', function (Blueprint $table) {
            if (! $this->hasIndex('contacts', 'contacts_mobile_index')) {
                $table->index('mobile');
            }
            if (! $this->hasIndex('contacts', 'contacts_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! $this->hasIndex('products', 'products_sku_index')) {
                $table->index('sku');
            }
        });

        Schema::table('variations', function (Blueprint $table) {
            if (! $this->hasIndex('variations', 'variations_sub_sku_index')) {
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
            if ($this->hasIndex('contacts', 'contacts_mobile_index')) {
                $table->dropIndex(['mobile']);
            }
            if ($this->hasIndex('contacts', 'contacts_name_index')) {
                $table->dropIndex(['name']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if ($this->hasIndex('products', 'products_sku_index')) {
                $table->dropIndex(['sku']);
            }
        });

        Schema::table('variations', function (Blueprint $table) {
            if ($this->hasIndex('variations', 'variations_sub_sku_index')) {
                $table->dropIndex(['sub_sku']);
            }
        });
    }
};
