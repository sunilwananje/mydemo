<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublishingQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_code_db',function(Blueprint $table){
            $table->increments('id');
            $table->string('shipper_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('partner_code');
            $table->timestamps();
        });

        Schema::create('process_queue',function(Blueprint $table){
            $table->increments('id');
            $table->integer('indexing_id')->unsigned()->nullable();
            $table->foreign('indexing_id')->references('id')->on('indexing')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->string('sq_no');
            $table->integer('publish_by')->unsigned()->nullable();
            $table->foreign('publish_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');

            $table->dateTime('publish_start_date');
            $table->dateTime('publish_end_date');

            $table->integer('request_id')->unsigned()->nullable();
            $table->foreign('request_id')->references('id')->on('mst_request_type')->onUpdate('NO ACTION')->onDelete('CASCADE');

            $table->mediumInteger('total_lane');
            $table->mediumInteger('no_of_inlands');
            $table->string('mode_id');
            $table->string('pricing_area');
            $table->dateTime('tat');
            $table->boolean('oot');
            $table->text('oot_remark');
            $table->text('comments');

            $table->integer('rfi_type_id')->unsigned()->nullable();
            $table->foreign('rfi_type_id')->references('id')->on('mst_rfi_type')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->string('isr_initiated');
            $table->text('rfi_description');
            $table->integer('rfi_raised_by')->unsigned()->nullable();
            $table->foreign('rfi_raised_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('rfi_resolved_by')->unsigned()->nullable();
            $table->foreign('rfi_resolved_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->string('rfi_comment');
            $table->dateTime('rfi_start_date');
            $table->dateTime('rfi_end_date');
            $table->integer('partner_code_db_id')->unsigned()->nullable();
            $table->string('error_description');
            $table->string('root_cause');
            $table->string('correction');
            $table->string('corrective_action');
            $table->string('preventive_action');
            $table->dateTime('proposed_comp_date');
            $table->dateTime('proposed_act_date');
            $table->tinyInteger('is_error');
            $table->integer('error_cat_id')->unsigned()->nullable();
            $table->foreign('error_cat_id')->references('id')->on('mst_error_cat')->onUpdate('NO ACTION')->onDelete('CASCADE');

            $table->integer('error_type_id')->unsigned()->nullable();
            $table->foreign('error_type_id')->references('id')->on('mst_error_type')->onUpdate('NO ACTION')->onDelete('CASCADE');

            $table->integer('error_done_by')->unsigned()->nullable();
            $table->foreign('error_done_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->integer('status_id')->unsigned()->nullable();
            $table->foreign('status_id')->references('id')->on('mst_status')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::drop('partner_code_db');
        Schema::drop('process_queue');
    }
}
