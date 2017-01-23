<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_office',function(Blueprint $table){
            $table->increments('id');
            $table->string('office_name');
            $table->string('office_address');
            $table->timestamps();
        });

        Schema::create('mst_holiday',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->date('holiday_date');
            $table->integer('office_id')->unsigned();
            $table->foreign('office_id')->references('id')->on('mst_office')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });

        Schema::create('mst_region',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('region_abbr');
            $table->integer('region_volume');
            $table->timestamps();
        });

        Schema::create('mst_request_type',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mst_container_type',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mst_error_type',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mst_priority_type',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mst_error_cat',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mst_modes',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('mst_menu',function(Blueprint $table){
            $table->increments('id');
            $table->string('menu_name');
            $table->integer('permission_id')->unsigned();
            $table->integer('parent_id')->unsigned();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_tat');
        Schema::drop('mst_office');
        Schema::drop('mst_holiday');
        Schema::drop('mst_region');
        Schema::drop('mst_request_type');
        Schema::drop('mst_container_type');
        Schema::drop('mst_error_type');
        Schema::drop('mst_priority_type');
        Schema::drop('mst_error_cat');
        Schema::drop('mst_modes');
        Schema::drop('mst_menu');
    }

}
