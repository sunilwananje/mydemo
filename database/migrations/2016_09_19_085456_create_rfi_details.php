<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfiDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfi_queue',function(Blueprint $table){
            $table->increments('id');
            $table->integer('indexing_id')->unsigned()->nullable();
            $table->foreign('indexing_id')->references('id')->on('indexing')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('process_queue_id')->unsigned()->nullable();
            $table->foreign('process_queue_id')->references('id')->on('process_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('audit_queue_id')->unsigned()->nullable();
            $table->foreign('audit_queue_id')->references('id')->on('audit_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');  
            $table->dateTime('rfi_start_date');
            $table->dateTime('rfi_end_date');
            $table->string('rfi_from');
            $table->tinyInteger('rfi_status');
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
        Schema::drop('rfi_details');
    }
}
