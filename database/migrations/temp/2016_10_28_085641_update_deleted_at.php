<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_status', function ($table) {
            $table->softDeletes();
        });

        Schema::table('audit_queue', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_container_type', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_error_cat', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_error_type', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_holiday', function ($table) {
            $table->softDeletes();
        });

       
        Schema::table('mst_modes', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_pricing_area', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_office', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_priority_type', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_tat', function ($table) {
            $table->softDeletes();
        }); 

        Schema::table('process_queue', function ($table) {
            $table->softDeletes();
        });

        Schema::table('mst_region', function ($table) {
            $table->softDeletes();
        });        

        Schema::table('mst_request_type', function ($table) {
            $table->softDeletes();
        });   

        Schema::table('mst_rfi_type', function ($table) {
            $table->softDeletes();
        });   
  
        Schema::table('users', function ($table) {
            $table->softDeletes();
        });



        Schema::table('pricer', function ($table) {
            $table->softDeletes();
        }); 

        Schema::table('partner_code_db', function ($table) {
            $table->softDeletes();
        });

        Schema::table('rfi_queue', function ($table) {
            $table->softDeletes();
        });   

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
