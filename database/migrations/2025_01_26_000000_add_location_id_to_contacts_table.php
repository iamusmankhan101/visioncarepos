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
            // Add location_id field to contacts table
            $table->integer('location_id')->unsigned()->nullable()->after('business_id');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
            
            // Add index for better performance
            $table->index(['business_id', 'location_id']);
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
            $table->dropForeign(['location_id']);
            $table->dropIndex(['business_id', 'location_id']);
            $table->dropColumn('location_id');
        });
    }
};