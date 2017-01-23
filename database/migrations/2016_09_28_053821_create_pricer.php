<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricer',function(Blueprint $table){
            $table->increments('id');
            $table->integer('indexing_id')->unsigned()->nullable();
            $table->foreign('indexing_id')->references('id')->on('indexing')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('process_queue_id')->unsigned()->nullable();
            $table->foreign('process_queue_id')->references('id')->on('process_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('audit_queue_id')->unsigned()->nullable();
            $table->foreign('audit_queue_id')->references('id')->on('audit_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');  
            $table->string('pol_port');
            $table->string('pol_region');
            $table->string('pod_port');
            $table->string('pod_region');
            $table->string('pricer_name');
            $table->integer('updated_by')->unsigned()->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            /*$table->char('reminder1_sent',4);
            $table->char('reminder2_sent',4);
            $table->dateTime('reminder1_actual_sent');
            $table->dateTime('reminder2_actual_sent');
            $table->integer('final_status')->unsigned()->nullable();
            $table->timestamps();*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pricer');
    }
}
