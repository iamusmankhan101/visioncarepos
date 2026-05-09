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
            // Right Eye - Distance
            $table->string('r_dist_sph')->nullable()->after('custom_field4');
            $table->string('r_dist_cyl')->nullable()->after('r_dist_sph');
            $table->string('r_dist_axis')->nullable()->after('r_dist_cyl');
            
            // Right Eye - Near
            $table->string('r_near_sph')->nullable()->after('r_dist_axis');
            $table->string('r_near_cyl')->nullable()->after('r_near_sph');
            $table->string('r_near_axis')->nullable()->after('r_near_cyl');
            
            // Left Eye - Distance
            $table->string('l_dist_sph')->nullable()->after('r_near_axis');
            $table->string('l_dist_cyl')->nullable()->after('l_dist_sph');
            $table->string('l_dist_axis')->nullable()->after('l_dist_cyl');
            
            // Left Eye - Near
            $table->string('l_near_sph')->nullable()->after('l_dist_axis');
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
            $table->dropColumn([
                'r_dist_sph',
                'r_dist_cyl',
                'r_dist_axis',
                'r_near_sph',
                'r_near_cyl',
                'r_near_axis',
                'l_dist_sph',
                'l_dist_cyl',
                'l_dist_axis',
                'l_near_sph',
            ]);
        });
    }
};
