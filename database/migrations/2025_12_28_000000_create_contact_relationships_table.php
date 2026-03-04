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
        Schema::create('contact_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('related_contact_id');
            $table->string('relationship_type')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->timestamps();

            // Indexes (without foreign key constraints to avoid errors)
            $table->index('contact_id');
            $table->index('related_contact_id');
            $table->index('business_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_relationships');
    }
};
