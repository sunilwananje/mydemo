<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusRfiUseraccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_status',function(Blueprint $table){
            $table->increments('id');
            $table->string('status_name');
            $table->string('status_type');
            $table->timestamps();
        });
             
        /*Schema::create('mst_commodity',function(Blueprint $table){
            $table->increments('id');
            $table->string('type');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });*/    

        Schema::create('mst_rfi_type',function(Blueprint $table){
            $table->increments('id');
            $table->string('rfi_type_name');
            $table->timestamps();
        });

        Schema::create('mst_tat',function(Blueprint $table){
            $table->increments('id');
            $table->integer('priority_id')->unsigned();
            $table->foreign('priority_id')->references('id')->on('mst_priority_type')->onDelete('cascade');
            $table->integer('tat_time');
            $table->string('agency_start_hrs')->nullable();
            $table->string('agency_end_hrs')->nullable();
            $table->timestamps();
        });
        

        Schema::create('mst_roles',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        /*Schema::create('mst_user_access',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('login_id');
            $table->integer('access_id')->unsigned()->nullable();
            $table->foreign('access_id')->references('id')->on('mst_access')->onUpdate('cascade')->onDelete('cascade');
            $table->string('email');
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('mst_status');
         Schema::drop('mst_status_cat');
         Schema::drop('statusCatId_statusId');
         Schema::drop('mst_commodity');
         Schema::drop('mst_tat');
         Schema::drop('mst_access');
         Schema::drop('mst_user_access');
    }
}
