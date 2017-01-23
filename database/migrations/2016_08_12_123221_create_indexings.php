<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indexing', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('mail_received_time');
            $table->dateTime('indexing_tat');
            $table->string('request_no',100)->unique();
            $table->string('customer_name',255);
            $table->integer('priority_id')->unsigned()->nullabel();
            $table->foreign('priority_id')->references('id')->on('mst_priority_type')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('region_id')->unsigned()->nullabel();
            $table->foreign('region_id')->references('id')->on('mst_region')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('request_type_id')->unsigned()->nullabel();
            $table->foreign('request_type_id')->references('id')->on('mst_request_type')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('office_id')->unsigned()->nullabel();
            $table->foreign('office_id')->references('id')->on('mst_office')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->text('comments');
            $table->integer('indexed_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        /*Schema::create('request_number',function(Blueprint $table){
            $table->increments('id');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('indexing');
       // Schema::drop('request_number');
    }
}
//DELETE FROM `migrations` WHERE `migration` ='2016_08_12_123221_create_indexings'