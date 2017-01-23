<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_queue',function(Blueprint $table){
        $table->increments('id');
        $table->integer('process_queue_id')->unsigned()->nullable();
        $table->foreign('process_queue_id')->references('id')->on('process_queue')->onUpdate('NO ACTION')->onDelete('CASCADE');
        $table->dateTime('send_audit_date');
        $table->integer('audit_by')->unsigned()->nullable();
        $table->foreign('audit_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        $table->dateTime('audit_start_date');
        $table->dateTime('audit_end_date');
        $table->boolean('oot');
        $table->text('oot_remark');
        $table->text('comments');

        $table->integer('audit_rfi_type_id')->unsigned()->nullable();
        $table->foreign('audit_rfi_type_id')->references('id')->on('mst_rfi_type')->onUpdate('NO ACTION')->onDelete('CASCADE');
        $table->text('audit_rfi_description');
        $table->string('audit_isr_initiated');
        $table->integer('audit_rfi_raised_by')->unsigned()->nullable();
        $table->foreign('audit_rfi_raised_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        $table->integer('audit_rfi_resolved_by')->unsigned()->nullable();
        $table->foreign('audit_rfi_resolved_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        $table->string('audit_rfi_comment');
        
        $table->dateTime('audit_rfi_start_date');
        $table->dateTime('audit_rfi_end_date');
        $table->tinyInteger('is_error');
        $table->integer('audit_error_cat_id')->unsigned()->nullable();
        $table->foreign('audit_error_cat_id')->references('id')->on('mst_error_cat')->onUpdate('NO ACTION')->onDelete('CASCADE');

        $table->integer('audit_error_type_id')->unsigned()->nullable();
        $table->foreign('audit_error_type_id')->references('id')->on('mst_error_type')->onUpdate('NO ACTION')->onDelete('CASCADE');
        
        $table->string('audit_error_description');
        $table->string('audit_root_cause');
        $table->string('audit_correction');
        $table->string('audit_corrective_action');
        $table->string('audit_preventive_action');
        $table->dateTime('audit_proposed_comp_date');
        $table->dateTime('audit_proposed_act_date');
        $table->integer('audit_error_done_by')->unsigned()->nullable();
        $table->foreign('audit_error_done_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        
        $table->integer('audit_status_id')->unsigned()->nullable();
        $table->foreign('audit_status_id')->references('id')->on('mst_status')->onUpdate('NO ACTION')->onDelete('CASCADE');
        $table->string('tat_complition',50);
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

  }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('audit_queue');
    }
}
