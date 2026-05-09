<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, add custom_field5-8 columns
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('custom_field5')->nullable()->after('custom_field4');
            $table->string('custom_field6')->nullable()->after('custom_field5');
            $table->string('custom_field7')->nullable()->after('custom_field6');
            $table->string('custom_field8')->nullable()->after('custom_field7');
        });

        // Migrate existing data from custom_field1-4 to optical prescription columns
        // custom_field1 -> r_dist_sph (Right Distance Sphere)
        // custom_field2 -> r_dist_cyl (Right Distance Cylinder)
        // custom_field3 -> r_dist_axis (Right Distance Axis)
        // custom_field4 -> r_near_sph (Right Near Sphere)
        
        DB::statement("
            UPDATE contacts 
            SET 
                r_dist_sph = custom_field1,
                r_dist_cyl = custom_field2,
                r_dist_axis = custom_field3,
                r_near_sph = custom_field4
            WHERE 
                custom_field1 IS NOT NULL 
                OR custom_field2 IS NOT NULL 
                OR custom_field3 IS NOT NULL 
                OR custom_field4 IS NOT NULL
        ");

        // Clear the old custom fields after migration
        DB::statement("
            UPDATE contacts 
            SET 
                custom_field1 = NULL,
                custom_field2 = NULL,
                custom_field3 = NULL,
                custom_field4 = NULL
            WHERE 
                r_dist_sph IS NOT NULL 
                OR r_dist_cyl IS NOT NULL 
                OR r_dist_axis IS NOT NULL 
                OR r_near_sph IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore data back to custom fields
        DB::statement("
            UPDATE contacts 
            SET 
                custom_field1 = r_dist_sph,
                custom_field2 = r_dist_cyl,
                custom_field3 = r_dist_axis,
                custom_field4 = r_near_sph
            WHERE 
                r_dist_sph IS NOT NULL 
                OR r_dist_cyl IS NOT NULL 
                OR r_dist_axis IS NOT NULL 
                OR r_near_sph IS NOT NULL
        ");

        // Drop the new custom field columns
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'custom_field5',
                'custom_field6',
                'custom_field7',
                'custom_field8',
            ]);
        });
    }
};
