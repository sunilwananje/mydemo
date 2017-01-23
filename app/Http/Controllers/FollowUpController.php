<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\AuditingQueue;
use App\Model\ProcessQueue;
use App\Model\Pricer;
use App\Model\PartnerCodeDb;
use App\Model\ReminderMailSetting;
use Session;

class FollowUpController extends Controller
{
    public function index()
    {
       $auditQueue = new AuditingQueue();
       $followUpData = $auditQueue->followUpList();
       return view('followup.followupView',compact('followUpData'));
    }

    public function sendReminder()
    {
		$auditQueue = new AuditingQueue();
	    $reminderData = $auditQueue->reminderList();
       // dd($reminderData);
	    date_default_timezone_set(TIME_ZONE);
		$cr_date = date("Y-m-d H:i:s");
		$past_date = date("Y-m-d H:i:s",strtotime($cr_date.' -6 minute'));
	    $body = 'followup.reminder'; //blade page path
	    $bodyData = array('name' => 'Admin');
        $emailIds = ReminderMailSetting::select('email')->get();
        $ccArray = array();
        foreach($emailIds as $k=>$v) {
                array_push($ccArray, $v->email);
            }
        $ccArray = array_values($ccArray);

        //dd($ccArray);
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
               // dd('dd',$ccArray);
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

	 // return '<h3>Reminder Sent...</h3>';
        return redirect()->back();

    }
    /*public function edit($id)
    {
    	$recId = region::findOrFail($id);
    	//dd($recId);
    	return view('masters.region.regionEdit',compact('recId'));
    }*/

    public function store(Request $req)
    {
    	$pricer = new Pricer();
    	$res = $pricer->validatePricer($req->all());

    	if ($res->fails()) {
            return redirect()->route('queue.followup')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $pricer->savePricer($req->all());
        	if($saveYN){
        		Session::flash('message','Pricer Added Successfully');
        		return redirect()->route('queue.followup');
        	}
        	else{

        		Session::flash('error','Pricer Not Added');
        		return redirect()->route('queue.followup');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$pricer = new Pricer();
    	$res = $pricer->validatePricer($req->all());

    	if ($res->fails()) {
            return redirect()->route('pricer.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $region->updatePricer($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Pricer Updated Successfully');
        		return redirect()->route('pricer.index');
        	}
        	else{

        		Session::flash('error','Pricer Not Updated');
        		return redirect()->route('pricer.index');	
        	}
        	
        }
    }
    public function updateStatus(Request $request)
    {
        if($request->aq_id){
           $audit = AuditingQueue::where('id', $request->aq_id) //update status to audit queue
        ->update(['audit_status_id' => $request->status, 'reminder1_sent'=>'Y', 'reminder2_sent'=>'Y']); 
        }else{
            $audit = ProcessQueue::where('id', $request->pq_id) //update status to audit queue
        ->update(['status_id' => $request->status, 'reminder1_sent'=>'Y', 'reminder2_sent'=>'Y']);
        }
    	
    	return $audit;
    }
    public function pricerData()
    {
    	$pricer = new Pricer();
    	$pricerData = $pricer->getPricer();
        return view('followup.pricer',compact('pricerData'));
    }
    public function partnerCodeData()
    {//get pricer table data
      $partnerData = PartnerCodeDb::get();
        return view('followup.partnercode',compact('partnerData'));
    }
}
