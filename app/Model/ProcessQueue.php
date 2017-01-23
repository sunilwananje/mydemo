<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Indexing;
use App\Model\PartnerCodeDb; 
use App\Model\AuditingQueue; 
use App\Classes\TatCalculator;
use App\Model\RFIQueue;
use Validator, Schema, Session;

class ProcessQueue extends Model
{
    /*use SoftDeletes;

    protected $dates = ['deleted_at'];*/
    protected $table = 'process_queue';
    public $columns = [];
    protected $rules = [
    	'mail_received_time'  => 'required',
     	'user_name'   => 'required',
     	//'sq_no' => 'required',
     	'request_type_id' => 'required',
     	'status_id'   => 'required',
     	'total_lane'   => 'required|numeric',
     	'no_of_inlands'   => 'required|numeric',
     	/*'modes'   => 'required',
     	'price_area'   => 'required',
     	'partner_code'   => 'required',
     	'shipper_name'   => 'required',
     	'city'   => 'required',
     	'state'   => 'required',
     	'address'   => 'required',*/     	
    ];

    protected $messages = [ //User defined message for errors
	    'mail_received_time.required'	=> 'The mail received date is required.',
	    'user_name.required'	=> 'The customer name is required.',
	    'priority_id.required' 	=> 'The priority is required.',
	 	'request_id.required'  => 'The requested type is required.',
	 	'status_id.required'    => 'The status is required.',
	];

    public function rfiType()
    {
        return $this->belongsTo('App\Model\RfiType','rfi_type_id', 'id');
    }

    public function errCat()
    {
        return $this->belongsTo('App\Model\ErrorCat','error_cat_id', 'id');
    }

    public function errType()
    {
        return $this->belongsTo('App\Model\ErrorType','error_type_id', 'id');
    }

    public function rfiDoneBy()
    {
        return $this->belongsTo('App\Model\userAccess','rfi_raised_by', 'id');
    }

    public function errorDoneBy()
    {
        return $this->belongsTo('App\Model\userAccess','error_done_by', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Model\Status','status_id', 'id');
    }
    public function partnerCodeDb()
    {
        return $this->belongsTo('App\Model\PartnerCodeDb','partner_code_db_id', 'id');
    }
    public function requestType()
    {
        return $this->belongsTo('App\Model\RequestType','request_id', 'id');
    }
	public function validateUPF(array $data,$id = null)
    {
    	$valid = $this->find($id);
    	if(!empty($data['request_type_id'])){   //set validation on change of request type
    		//$ind->request_type_id = $data['request_type_id'];
	    	$rquest_name = strtolower($data['request_type_id']);

	    	if($rquest_name=='rfi'){
	            $this->rules['rfi_type_id']='required';
	    		$this->rules['rfi_description']='required';
	    	}

	    	if($rquest_name=='cor' || isset($data['is_error'])){
	        $this->rules['error_cat_id']='required';
	    		$this->rules['error_type_id']='required';
	    		$this->rules['error_description']='required';
	    		$this->rules['root_cause']='required';
	    		$this->rules['correction']='required';
	    		$this->rules['corrective_action']='required';
	    		$this->rules['preventive_action']='required';
	    		$this->rules['proposed_comp_date']='required';
	    		$this->rules['proposed_act_date']='required';
	    		$this->rules['error_done_by']='required';
	    	}
    	}
        if(!empty($data['status_id'])){ //set validation on change of status id
            $this->status_id = $data['status_id'];
            $status_type = strtolower($this->status->status_name);

            if($status_type == 'pending in'){

                $this->rules['rfi_type_id']='required';
                $this->rules['rfi_description']='required';
                $this->rules['isr_initiated']='required';

            }elseif($status_type == 'pending out'){

                $pendin_in_date = date('d-m-Y H:i',strtotime($valid->rfi_start_date));
                $this->rules['rfi_etd'] = 'required|date|after:'.$pendin_in_date;
                $this->messages['rfi_etd.date'] = 'Invalid date';
                $this->messages['rfi_etd.after'] = 'Date must be greater than pending in date '.$pendin_in_date;

            }elseif($status_type != 'in process'){

                $this->rules['sq_no'] = 'required';
                $this->rules['modes'] = 'required';
                $this->rules['price_area'] = 'required';
                $this->rules['partner_code'] = 'required';
                $this->rules['shipper_name'] = 'required';
                $this->rules['city']  = 'required';
                $this->rules['state'] = 'required';
                $this->rules['address'] = 'required';

            }
        }
    	
    	return  Validator::make($data, $this->rules,$this->messages);
    	
    }

    public function saveUPF(array $data)
    {

    	$columns = Schema::getColumnListing($this->table);
        
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns) && !empty($value)){
    			$this->$key = $value;
    		}
    	}
    	return $this->save();
    }

    public function updateUPF(array $data, $id)
    {
    	date_default_timezone_set(TIME_ZONE);
    	$cr_date=date("Y-m-d H:i:s");
      $mode = $pricing_area = '';
    	$columns = Schema::getColumnListing($this->table);
      $tat_cal = new TatCalculator();
    	$obj = $this->find($id);

    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns) && !empty($value) ){
    			$obj->$key = $value;
    		}
    	}
        if(isset($data['modes'])){
           foreach($data['modes'] as $value){
            $mode .= $value.',';
           } 
           $obj->mode_id = substr($mode,0,-1);
        
        }
    	if(isset($data['price_area'])){
        	foreach($data['price_area'] as $value){
        		$pricing_area .= $value.',';
        	}
            $obj->pricing_area = substr($pricing_area,0,-1);
        }
    	
    	if(!empty($data['request_type_id'])){
    		//$ind = new Indexing();
    		//$ind->request_type_id = $data['request_type_id'];
	    	$rquest_name = strtolower($data['request_type_id']);
	 
	    	if($rquest_name=='cor' || isset($data['is_error'])){
	    		$obj->is_error =1;
	    	}
	    }
    	if(!empty($data['rfi_type_id'])){
    		$obj->rfi_raised_by = Session::get('user_id');
    	}
    	if(!empty($data['error_type_id'])){
    		$obj->proposed_comp_date = date('Y-m-d H:i:s',strtotime($data['proposed_comp_date']));
    		$obj->proposed_act_date = date('Y-m-d H:i:s',strtotime($data['proposed_act_date']));

    	}
        
    	
        
        $result = PartnerCodeDb::where('partner_code',$data['partner_code'])->first();
        $data['address'] = str_replace(array("\n\r", "\n", "\r"), '', $data['address']);

        if(!$result){
        	$pc = new PartnerCodeDb();
        	$pc->shipper_name = $data['shipper_name'];
        	$pc->address = $data['address'];
        	$pc->city = $data['city'];
        	$pc->state = $data['state'];
        	$pc->partner_code = $data['partner_code'];
        	$pc->save();
        	$pcid=$pc->id;
        }else{
        	$result->shipper_name = $data['shipper_name'];
        	$result->address = $data['address'];
        	$result->city = $data['city'];
        	$result->state = $data['state'];
        	$result->save();
        	$pcid=$result->id;
        }
       
        $obj->partner_code_db_id = $pcid;
         
         if(in_array($obj->status->status_name, array('sent to audit','sent to pricer','done','disregard'))) //if status is in mentioned array then publish request stops
            $obj->publish_end_date = date("Y-m-d H:i:s");//capture publish end time
     
         if($obj->status->status_name === 'sent to pricer' || $obj->status->status_name === 'done'){ //update folloup field
            $obj->follow_up_date = $cr_date;
            //$obj->reminder_1 = date('Y-m-d H:i:s',strtotime($cr_date.' +24 Hours'));
            //$obj->reminder_2 = date('Y-m-d H:i:s',strtotime($cr_date.' +48 Hours'));
            $obj->reminder_1 = $tat_cal->calculateTat(24,$cr_date);
            $obj->reminder_2 = $tat_cal->calculateTat(24,$obj->reminder_1);
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
         	    $rfiq = RFIQueue::where('process_queue_id',$id)->first();
	         	if(empty($rfiq->id)){
	           	  $rfiq = new RFIQueue();
	            }
                $rfiq = new RFIQueue();
	            $rfiq->indexing_id = $data['indexing_id'];
	            $rfiq->process_queue_id = $id;
	            $rfiq->rfi_start_date = $cr_date;
	            $rfiq->rfi_status = 1;
	            $rfiq->rfi_from = 'pq';
	            $rfiq->save();
	            $obj->rfi_start_date = $cr_date;//capture rfi start time
         } 
            
         if($obj->status->status_name === 'pending out'){
            if(!empty($data['rfi_id'])){
                $obj->rfi_end_date = date('Y-m-d H:i:s',strtotime($data['rfi_etd']));//$cr_date;
                $rfiq = RFIQueue::where('id',$data['rfi_id']);
                $rfiq->update(['rfi_end_date'=>$obj->rfi_end_date,'rfi_status'=>2,'rfi_from'=>'pq']);
                    /*if(!empty($rfiq->id)){
                        $rfiq->rfi_end_date = $obj->rfi_end_date;
                        $rfiq->rfi_status = 2;
                        $rfiq->rfi_from = 'pq';
                        $rfiq->save();
                        dd($rfiq->id);
                    }*/
                $indexing = Indexing::find($data['indexing_id']);
                
                $indexing->indexing_tat = $tat_cal->rfiDeadline($obj->rfi_start_date, $obj->rfi_end_date, $indexing->indexing_tat);
                $indexing->save();
                $obj->status_id = 1;
            }
         	
         	//echo "sserr";
         }

         if($obj->status->status_name === 'sent to audit'){
         	$auditing = new AuditingQueue();
         	$auditing->process_queue_id = $id;
         	$auditing->send_audit_date = $cr_date;
         	$auditing->save();
         }  
             
         if(!empty($data['proposed_comp_date'])){
             $obj->proposed_comp_date = date("Y-m-d H:i:s",strtotime($data['proposed_comp_date']));
         }
         if(!empty($data['proposed_act_date'])){
         	 $obj->proposed_act_date = date("Y-m-d H:i:s",strtotime($data['proposed_act_date']));
         }

         if(is_numeric($data['user_name'])){
            
            if($data['user_name'] != $obj->publish_by){
              $obj->status_id = 1;  
              $obj->publish_start_date = $cr_date;  
            }
            $obj->publish_by = $data['user_name'];
         }
         //dd($data['address']);
    	return $obj->save();
    }
    
}
