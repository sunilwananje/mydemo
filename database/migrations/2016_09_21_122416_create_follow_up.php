<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowUp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_up',function(Blueprint $table){
            $table->increments('id');
            $table->integer('indexing_id')->unsigned()->nullable();
            $table->foreign('indexing_id')->references('id')->on('indexing')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('process_queue_id')->unsigned()->nullable();
            $table->foreign('process_queue_id')->references('id')->on('process_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('audit_queue_id')->unsigned()->nullable();
            $table->foreign('audit_queue_id')->references('id')->on('audit_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');  
            $table->dateTime('follow_up_date');
            $table->dateTime('reminder_1');
            $table->dateTime('reminder_2');
            $table->char('reminder1_sent',4);
            $table->char('reminder2_sent',4);
            $table->dateTime('reminder1_actual_sent');
            $table->dateTime('reminder2_actual_sent');
            $table->integer('final_status')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('user_logged_details',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->dateTime('login_time');
            $table->dateTime('logout_time');
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
        Schema::drop('follow_up');
        Schema::drop('user_logged_details');
    }
}
