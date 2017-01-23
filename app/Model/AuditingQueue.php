<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Indexing;
use App\Model\ProcessQueue; 
use App\Classes\TatCalculator;
use App\Model\RFIQueue;
use Validator, Schema, Session, Mail;

class AuditingQueue extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'audit_queue';
    
    public $columns = [];

    protected $rules = [
    	'mail_received_time'  => 'required',
     	'user_name'   => 'required',
     	'publisher_name'   => 'required|min:2|max:255',
     	'account_name' => 'required|min:2|max:255',
     	'request_type_id' => 'required',
     	'audit_status_id'   => 'required', 	
    ];

    protected $messages = [ //User defined message for errors
	    'mail_received_time.required'	=> 'The mail received date is required.',
	    'user_name.required'	=> 'The customer name is required.',
	    'priority_id.required' 	=> 'The priority is required.',
	 	'request_id.required'  => 'The requested type is required.',
        'status_id.required'    => 'The office is required.',
	];

    public function rfiType()
    {
        return $this->belongsTo('App\Model\RfiType','audit_rfi_type_id', 'id');
    }

    public function errCat()
    {
        return $this->belongsTo('App\Model\ErrorCat','audit_error_cat_id', 'id');
    }

    public function errType()
    {
        return $this->belongsTo('App\Model\ErrorType','audit_error_type_id', 'id');
    }

    public function rfiDoneBy()
    {
        return $this->belongsTo('App\Model\userAccess','audit_rfi_raised_by', 'id');
    }

    public function errorDoneBy()
    {
        return $this->belongsTo('App\Model\userAccess','audit_error_done_by', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Model\Status','audit_status_id', 'id');
    }
    public function requestType()
    {
        return $this->belongsTo('App\Model\RequestType','request_id', 'id');
    }
    public function auditingList($audit_id=0){
        $auditingQuery = Indexing::join('process_queue','indexing.id', '=', 'process_queue.indexing_id');
        $auditingQuery->join('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id');
        $auditingQuery->join('users as publisher','publisher.id', '=', 'process_queue.publish_by');
        $auditingQuery->leftJoin('mst_status','mst_status.id', '=', 'audit_queue.audit_status_id');

        $auditingQuery->leftJoin('mst_status as publish_status','publish_status.id', '=', 'process_queue.status_id');

        $auditingQuery->leftJoin('users as auditor','auditor.id', '=', 'audit_queue.audit_by');
        $auditingQuery->leftJoin('users as corrector','corrector.id', '=', 'audit_queue.audit_error_done_by');
        $auditingQuery->leftJoin('users as resolver','resolver.id', '=', 'audit_queue.audit_rfi_resolved_by');
        $auditingQuery->leftJoin('users as questioner','questioner.id', '=', 'audit_queue.audit_rfi_raised_by');
        
        $auditingQuery->leftJoin('mst_rfi_type','mst_rfi_type.id', '=', 'audit_queue.audit_rfi_type_id');

        $auditingQuery->leftJoin('mst_error_cat','mst_error_cat.id', '=', 'audit_queue.audit_error_cat_id');
        $auditingQuery->leftJoin('mst_error_type','mst_error_type.id', '=', 'audit_queue.audit_error_type_id');
        
        $auditingQuery->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id');
        $auditingQuery->leftJoin('mst_priority_type','mst_priority_type.id', '=', 'indexing.priority_id');
        $auditingQuery->leftJoin('mst_request_type','mst_request_type.id', '=', 'indexing.request_type_id');

        $auditingQuery->select('audit_queue.*','process_queue.sq_no','process_queue.total_lane','process_queue.no_of_inlands','process_queue.mode_id','process_queue.pricing_area','partner_code_db_id','process_queue.publish_by');

        $auditingQuery->addSelect('indexing.id as indexing_id','indexing.mail_received_time','indexing.indexing_tat','indexing.request_no','indexing.customer_name','indexing.priority_id','indexing.region_id','indexing.office_id','indexing.indexed_by','indexing.request_type_id','mst_region.name as region_name','mst_priority_type.name as priority_type','mst_request_type.name as request_type');

        $auditingQuery->addSelect('mst_rfi_type.rfi_type_name as audit_rfi_name','mst_error_cat.name as audit_err_cat_name', 'mst_error_type.name as audit_err_type_name', 'auditor.name as auditor_name', 'corrector.name as corrector_name','publisher.name as publisher_name','resolver.name as rfi_resolver_name','questioner.name as rfi_questioner_name','mst_status.status_name as audit_status_name', 'publish_status.status_name as publish_status_name', 'process_queue.id as process_queue_id');
    	$auditingQuery->whereNotIn('mst_status.status_name',['pending in','sent to pricer','sent to customer','sent to inside sales','done','disregard']);
    	$auditingQuery->orWhereNull('mst_status.status_name');
        $auditingQuery->orderBy('indexing.priority_id','DESC');
        $auditingQuery->orderBy('indexing.mail_received_time','DESC');

        if($audit_id!=0){
            $auditings = $auditingQuery->where('audit_queue.id',$audit_id)->first();
        }else{
        	$auditings = $auditingQuery->get();
        }
        
        return $auditings;
    }
    
	public function validateAPF(array $data,$id=0)
    {

    	$valid = $this->find($id);
    	if(!empty($data['request_type_id'])){
    		//$ind->request_type_id = $data['request_type_id'];
	    	$rquest_name = strtolower($data['request_type_id']);

	    	if($rquest_name=='rfi'){
	            $this->rules['audit_rfi_type_id']='required';
	    		$this->rules['audit_rfi_description']='required';
	    	}
	    	if($rquest_name=='cor' || isset($data['is_error'])){
	    		$this->rules['audit_error_cat_id']='required';
	    		$this->rules['audit_error_type_id']='required';
	    		$this->rules['audit_error_description']='required';
	    		$this->rules['audit_root_cause']='required';
	    		$this->rules['audit_correction']='required';
	    		$this->rules['audit_corrective_action']='required';
	    		$this->rules['audit_preventive_action']='required';
	    		$this->rules['audit_proposed_comp_date']='required';
	    		$this->rules['audit_proposed_act_date']='required';
	    		$this->rules['audit_error_done_by']='required';
	    	}
    	}
        if(!empty($data['audit_status_id'])){
            $this->audit_status_id = $data['audit_status_id'];
            //dd($data['rfi_etd']);
            //$ind->request_type_id = $data['request_type_id'];
            $status_type = strtolower($this->status->status_name);

            if($status_type == 'pending in'){
                $this->rules['audit_rfi_type_id']='required';
                $this->rules['audit_rfi_description']='required';
                $this->rules['audit_isr_initiated']='required';
            }
            if($status_type == 'pending out'){
                $pendin_in_date = date('d-m-Y H:i',strtotime($valid->audit_rfi_start_date));
               // dd($status_type,$pendin_in_date);
                
                //$this->rules['audit_rfi_comment'] = 'required';
                $this->rules['rfi_etd'] = 'required|date|after:'.$pendin_in_date;
                $this->messages['rfi_etd.date'] = 'Invalid date';
                $this->messages['rfi_etd.after'] = 'Date must be greater than pending in date '.$pendin_in_date;
            }
        }
    	//dd(Validator::make($data, $this->rules,$this->messages));
    	return  Validator::make($data, $this->rules,$this->messages);
    	
    }

    public function saveAPF(array $data)
    {

    	$columns = Schema::getColumnListing($this->table);
        
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns) && !empty($value)){
    			$this->$key = $value;
    		}
    	}
    	
    	return $this->save();
    }

    public function updateAPF(array $data, $id)
    {
    	date_default_timezone_set(TIME_ZONE);
    	$cr_date=date("Y-m-d H:i:s");
        $mode = $pricing_area = '';
    	$columns = Schema::getColumnListing($this->table);
        $tat_cal = new TatCalculator();
    	$obj = $this->find($id);

    	foreach ($data as $key => $value) {   // getting all input data
    		if(in_array($key, $columns) && !empty($value) ){
    			$obj->$key = $value;
    		}
    	}

    	
    	if(!empty($data['audit_rfi_type_id'])){    //if rfi is raised then capture user_id of current user
    		$obj->audit_rfi_raised_by = Session::get('user_id');
    	}
    	if(!empty($data['audit_error_type_id'])){ //is there any error the convert datetime in db format i.e Y-m-d
    		$obj->audit_proposed_comp_date = date('Y-m-d H:i:s',strtotime($data['audit_proposed_comp_date']));
    		$obj->audit_proposed_act_date = date('Y-m-d H:i:s',strtotime($data['audit_proposed_act_date']));

    	}

        if(!empty($data['total_lane'])){ //update total_lane,no_of_inland and modes in publish table
            $upfObj = ProcessQueue::find($obj->process_queue_id); 
            $upfObj->total_lane = $data['total_lane'];
            $upfObj->no_of_inlands = $data['no_of_inlands'];

            foreach($data['modes'] as $value){
               $mode .= $value.',';
            }
            $upfObj->mode_id = substr($mode,0,-1);

            /*foreach($data['price_area'] as $value){
                $pricing_area .= $value.',';
            }
            $upfObj->pricing_area = substr($pricing_area,0,-1);*/

            
            
            $upfObj->save();
        }
        
    	if(!empty($data['request_type_id'])){ 
    		//$ind = new Indexing();
    		//$ind->request_type_id = $data['request_type_id'];
	    	$rquest_name = strtolower($data['request_type_id']);
	 
	    	if($rquest_name=='cor' || isset($data['is_error'])){
	    		$obj->is_error = 1;
	    	}
	    }

         if(in_array($obj->status->status_name, array('sent to pricer','done','disregard')))
            $obj->audit_end_date = $cr_date; //set audit end date
         
         if($obj->status->status_name === 'sent to pricer'){ //update folloup field
            $obj->follow_up_date = $cr_date;
            $obj->reminder_1 = $tat_cal->calculateTat(24,$cr_date);
            $obj->reminder_2 = $tat_cal->calculateTat(24,$obj->reminder_1);
            //$obj->reminder_1 = date('Y-m-d H:i:s',strtotime($cr_date.' +24 Hours'));
            //$obj->reminder_2 = date('Y-m-d H:i:s',strtotime($cr_date.' +48 Hours'));
            $obj->reminder1_sent = 'N';
            $obj->reminder2_sent = 'N';
            $obj->tat_complition = $data['tat_comp'];
         }
         if($obj->status->status_name === 'sent to customer' || $obj->status->status_name === 'sent to inside sales'){
            $obj->follow_up_date = '0000-00-00 00:00:00';
            $obj->reminder_1 = '0000-00-00 00:00:00';
            $obj->reminder_2 = '0000-00-00 00:00:00';
            $obj->reminder1_sent = 'Y';
            $obj->reminder2_sent = 'Y';
            //$obj->final_status = 'Y';
         }

         if($obj->status->status_name === 'pending in'){
	         	/*$rfiq = RFIQueue::where('audit_queue_id',$id)->first();
	         	if(empty($rfiq->id)){
	           	  $rfiq = new RFIQueue();
	            }*/
                $rfiq = new RFIQueue();
	            $rfiq->indexing_id = $data['indexing_id'];
	            $rfiq->audit_queue_id = $id;
	            $rfiq->rfi_start_date = $cr_date;
	            $rfiq->rfi_status = 1;
	            $rfiq->rfi_from = 'aq';
	            $rfiq->save();

         	$obj->audit_rfi_start_date = $cr_date;
         } 
            //capture rfi start time

         if($obj->status->status_name === 'pending out'){
            if(!empty($data['rfi_id'])){
         	    $obj->audit_rfi_end_date = date('Y-m-d H:i:s',strtotime($data['rfi_etd']));//$cr_date;
                $rfiq = RFIQueue::where('id',$data['rfi_id']);
                $rfiq->update(['rfi_end_date'=>$obj->audit_rfi_end_date,'rfi_status'=>2,'rfi_from'=>'aq']);
                //$rfiq = RFIQueue::where('audit_queue_id',$id)->first();
         	    /*if(!empty($rfiq->id)){
		            $rfiq->rfi_end_date = $obj->audit_rfi_end_date;
		            $rfiq->rfi_status = 2;
		            $rfiq->rfi_from = 'aq';
		            $rfiq->save();
		        }*/
             	$indexing = Indexing::find($data['indexing_id']);
             	
                
             	$indexing->indexing_tat = $tat_cal->rfiDeadline($obj->audit_rfi_start_date, $obj->audit_rfi_end_date, $indexing->indexing_tat);
             	$indexing->save();
                $obj->audit_status_id = 8;
            }
         } 
             
         if(!empty($data['audit_proposed_comp_date'])){
             $obj->audit_proposed_comp_date = date("Y-m-d H:i:s",strtotime($data['audit_proposed_comp_date']));
         }
         if(!empty($data['audit_proposed_act_date'])){
         	 $obj->audit_proposed_act_date = date("Y-m-d H:i:s",strtotime($data['audit_proposed_act_date']));
         }

         if(is_numeric($data['user_name'])){
            
            if($data['user_name'] != $obj->audit_by){
              $obj->audit_status_id = 8;  
              $obj->audit_start_date = $cr_date;  
            }
            $obj->audit_by = $data['user_name'];
         }
            
    	return $obj->save();
    }

    public function followUpList(){
        $auditingQuery = Indexing::join('process_queue','indexing.id', '=', 'process_queue.indexing_id');
        $auditingQuery->leftJoin('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id');
        $auditingQuery->leftJoin('users as publisher','publisher.id', '=', 'process_queue.publish_by');
        $auditingQuery->leftJoin('mst_status','mst_status.id', '=', 'audit_queue.audit_status_id');
        $auditingQuery->leftJoin('mst_status as pq_status','pq_status.id', '=', 'process_queue.status_id');
        $auditingQuery->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id');
        $auditingQuery->leftJoin('mst_priority_type','mst_priority_type.id', '=', 'indexing.priority_id');

        $auditingQuery->select('audit_queue.follow_up_date','audit_queue.reminder_1','audit_queue.reminder_2','audit_queue.reminder1_sent','audit_queue.reminder2_sent','audit_queue.reminder1_actual_sent','audit_queue.reminder2_actual_sent','process_queue.sq_no','process_queue.publish_by','audit_queue.final_status','publisher.name as publisher_name','audit_queue.id as audit_queue_id');

        $auditingQuery->addSelect('process_queue.follow_up_date as pq_follow_up_date','process_queue.reminder_1 as pq_reminder_1','process_queue.reminder_2 as pq_reminder_2','process_queue.reminder1_sent as pq_reminder1_sent','process_queue.reminder2_sent as pq_reminder2_sent','process_queue.reminder1_actual_sent as pq_reminder1_actual_sent','process_queue.reminder2_actual_sent as pq_reminder2_actual_sent','process_queue.id as process_queue_id');

        $auditingQuery->addSelect('indexing.id as indexing_id','indexing.mail_received_time','indexing.indexing_tat','indexing.request_no','indexing.customer_name','indexing.priority_id','indexing.region_id','indexing.request_type_id','mst_priority_type.name as priority_type','mst_region.name as region_name');

        $auditingQuery->addSelect('mst_status.status_name as audit_status_name','pq_status.status_name as pq_status_name','audit_queue.audit_status_id','process_queue.status_id');

        $auditingQuery->where('mst_status.status_name','sent to pricer');
        $auditingQuery->orWhere('pq_status.status_name','sent to pricer');

        $auditings = $auditingQuery->get();
        
        
        return $auditings;
    }

    public function reminderList(){
        /*$auditingQuery = Indexing::join('process_queue','indexing.id', '=', 'process_queue.indexing_id');
        $auditingQuery->join('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id');
        $auditingQuery->join('users as publisher','publisher.id', '=', 'process_queue.publish_by');
        $auditingQuery->select('audit_queue.follow_up_date','audit_queue.reminder_1','audit_queue.reminder_2','audit_queue.reminder1_sent','audit_queue.reminder2_sent','process_queue.sq_no','process_queue.publish_by','audit_queue.final_status','publisher.name as publisher_name','publisher.email','publisher.username','indexing.request_no','audit_queue.id as audit_id','indexing.customer_name');
        $auditingQuery->where('audit_queue.reminder1_sent','N');
        $auditingQuery->orWhere('audit_queue.reminder2_sent','N');
        $auditings = $auditingQuery->get();*/
        $auditingQuery = Indexing::join('process_queue','indexing.id', '=', 'process_queue.indexing_id');
        $auditingQuery->leftJoin('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id');
        $auditingQuery->leftJoin('users as publisher','publisher.id', '=', 'process_queue.publish_by');
        
        $auditingQuery->select('audit_queue.follow_up_date','audit_queue.reminder_1','audit_queue.reminder_2','audit_queue.reminder1_sent','audit_queue.reminder2_sent','process_queue.sq_no','process_queue.publish_by','audit_queue.final_status','publisher.name as publisher_name','publisher.email','publisher.username','indexing.request_no','audit_queue.id as audit_id','indexing.customer_name');

        $auditingQuery->addSelect('process_queue.follow_up_date as pq_follow_up_date','process_queue.reminder_1 as pq_reminder_1','process_queue.reminder_2 as pq_reminder_2','process_queue.reminder1_sent as pq_reminder1_sent','process_queue.reminder2_sent as pq_reminder2_sent','process_queue.reminder1_actual_sent as pq_reminder1_actual_sent','process_queue.reminder2_actual_sent as pq_reminder2_actual_sent','process_queue.id as process_queue_id');

        $auditingQuery->where('audit_queue.reminder1_sent','N');
        $auditingQuery->orWhere('audit_queue.reminder2_sent','N');
        $auditingQuery->orWhere('process_queue.reminder1_sent','N');
        $auditingQuery->orWhere('process_queue.reminder2_sent','N');

        $auditings = $auditingQuery->get();

        return $auditings;
    }

    public function sendMail($body,$bodyData,$to,$sub,$toName,$ccArray){
        //dd($ccArray);
        $mail = Mail::send($body,$bodyData, function ($message) use ($to,$sub,$toName,$ccArray) {
            //$toArray = [$to];
            //'ssc.athaslimsalaga@cma-cgm.com', 'ssc.pmuneeswaran@cma-cgm.com',
            //$ccArray = ['ssc.dghadigaonkar@cma-cgm.com', 'ssc.USinsalesF2F@cma-cgm.com', 'ssc.gmanetee@cma-cgm.com', 'ssc.mbhosle@cma-cgm.com', 'ssc.hsalekar@cma-cgm.com', 'ext.swananje@cma-cgm.com'];
            $message->from('no-reply@cma-cgm.com', 'F2F SQ Creation');
            $message->to($to,$toName);
            $message->cc($ccArray);
            $message->subject($sub);
        });
        return $mail;
    }

   
}
