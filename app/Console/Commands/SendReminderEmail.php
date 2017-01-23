<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\AuditingQueue;
use App\Model\ProcessQueue;
use App\Model\ReminderMailSetting;

class SendReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder trigger';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $auditQueue = new AuditingQueue();
        $reminderData = $auditQueue->reminderList();
        //date_default_timezone_set(TIME_ZONE);
        $cr_date = date("Y-m-d H:i:s");
        $past_date = date("Y-m-d H:i:s",strtotime($cr_date.' -7 minute'));
        $body = 'followup.reminder'; //blade page path
        $bodyData = array('name' => 'Admin');
        $emailIds = ReminderMailSetting::select('email')->get();
        $ccArray = array();
        foreach($emailIds as $k=>$v) {
                array_push($ccArray, $v->email);
            }
        $ccArray = array_values($ccArray);
        //$ccArray = ['ssc.dghadigaonkar@cma-cgm.com', 'ssc.USinsalesF2F@cma-cgm.com', 'ssc.gmanetee@cma-cgm.com', 'ssc.mbhosle@cma-cgm.com', 'ssc.hsalekar@cma-cgm.com', 'ext.swananje@cma-cgm.com'];
        foreach($reminderData as $reminder){
             $mail = false;
             $to = $reminder->email;
             $toName = $reminder->publisher_name;
             if($reminder->audit_id){
                $auditObj = AuditingQueue::find($reminder->audit_id);
                $reminder1_sent = $reminder->reminder1_sent;
                $reminder2_sent = $reminder->reminder2_sent;
                $reminder_1 = $reminder->reminder_1;
                $reminder_2 = $reminder->reminder_2;
             }else{
                $auditObj = ProcessQueue::find($reminder->process_queue_id);
                $reminder1_sent = $reminder->pq_reminder1_sent;
                $reminder2_sent = $reminder->pq_reminder2_sent;
                $reminder_1 = $reminder->pq_reminder_1;
                $reminder_2 = $reminder->pq_reminder_2;
             }
               
             $bodyData['request_no'] = $reminder->request_no;
              if($reminder1_sent == 'N'){  //send reminder1 mail
                 if($reminder_1 >= $past_date && $reminder_1 <= $cr_date){
                    $auditObj->reminder1_sent = 'Y';
                    $auditObj->reminder1_actual_sent = $cr_date;
                    $sub = $reminder->sq_no.'-'.$reminder->customer_name.'- Reminder1';
                    $mail = $auditQueue->sendMail($body,$bodyData,$to,$sub,$toName,$ccArray);
                 }
              }elseif($reminder2_sent == 'N'){ //send reminder2 mail
                 if($reminder_2 >= $past_date && $reminder_2 <= $cr_date){
                    $auditObj->reminder2_sent = 'Y';
                    $auditObj->reminder2_actual_sent = $cr_date;
                    $sub = $reminder->sq_no.'-'.$reminder->customer_name.'- Reminder2';
                    $mail = $auditQueue->sendMail($body,$bodyData,$to,$sub,$toName,$ccArray);
                 }
              }
              if($mail){
                 $auditObj->save();
              }
                
        }
        $this->info('Command is working '.$cr_date);
    }
}
