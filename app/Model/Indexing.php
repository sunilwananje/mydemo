<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\PriorityType;
use App\Model\Region;
use App\Model\RequestType;
use App\Model\ContainerType;
use App\Model\Office;
use App\Classes\TatCalculator;
use Validator, Schema, Session;

class Indexing extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'indexing';

    public $columns = [];

    protected $rules = [
    	'mail_received_time'  => 'required',
     	'customer_name'   => 'required|min:2|max:255',
     	//'request_no'  => 'required',
     	'priority_id' => 'required',
     	'region_id'   => 'required',
     	'request_type_id' => 'required',
     	'office_id'   => 'required'
    ];

    protected $messages = [ //User defined message for errors
	    'mail_received_time.required'	=> 'The mail received date is required.',
	    'customer_name.required'	=> 'The customer name is required.',
	    'priority_id.required' 	=> 'The priority is required.',
	 	'region_id.required'    => 'The region is required.',
	 	'request_type_id.required'  => 'The requested type is required.',
	 	'office_id.required'    => 'The office is required.',
	];

	 /*Relationaships rule for indexing starts here*/    
	public function priority()
    {
    	return $this->belongsTo('App\Model\PriorityType','priority_id','id');

    }

    public function region()
    {
    	return $this->belongsTo('App\Model\Region','region_id','id');

    }

    public function requestType()
    {
    	return $this->belongsTo('App\Model\RequestType','request_type_id', 'id');

    }

    /*public function containerType()
    {
    	return $this->belongsTo('App\Model\ContainerType','cont_type_id','id');

    }*/

    public function office()
    {
    	return $this->belongsTo('App\Model\Office','office_id','id');

    }

    public function tat()
    {
        return $this->belongsTo('App\Model\Tat','priority_id','priority_id');

    }

    public function validateIndexing(array $data)
    {
    	
    	return  Validator::make($data, $this->rules,$this->messages);
    	
    }

    public function saveIndexing(array $data)
    {

    	$columns = Schema::getColumnListing($this->table);
      
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}
    	}
    	$tat_cal = new TatCalculator();
        $priorityHrs = Tat::where('priority_id',$data['priority_id'])->get()->toArray()[0]['tat_time'];
        $deadline = $tat_cal->calculateTat($priorityHrs, $data['mail_received_time']);
        
        $this->indexing_tat = $deadline;
    	$this->indexed_by = Session::get('user_id');
        $this->mail_received_time = date('Y-m-d H:i:s',strtotime($data['mail_received_time']));

        $result = $this->save();
        $request_no = '';
        if($result){
            $id = $this->id;
            $week = date('W', strtotime($data['mail_received_time'])); //week no
            $day = date('d', strtotime($data['mail_received_time'])); //day
            $month = date('m', strtotime($data['mail_received_time'])); //month
            $request_no = "WK$week/$day/$month#$id"; 
            $this->where('id',$id)->update(['request_no'=>$request_no]);
            
        }

        return $request_no;
        
    	//return $this->save();
    }

    public function updateIndexing(array $data, $id)
    {
    	//get all columns of current model

    	$columns = Schema::getColumnListing($this->table);

    	$obj = $this->find($id);

    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$obj->$key = $value;
    		}
    	}
    	$tat_cal = new TatCalculator();
        $priorityHrs = Tat::where('priority_id',$data['priority_id'])->get()->toArray()[0]['tat_time'];
        $deadline = $tat_cal->calculateTat($priorityHrs, $data['mail_received_time']);
       // 
        $obj->indexing_tat = $deadline;

    	$obj->mail_received_time = date('Y-m-d H:i:s',strtotime($data['mail_received_time']));
        //dd($obj->mail_received_time,$data['mail_received_time']);
        $obj->updated_by = Session::get('user_id');
    	return $obj->save();
    }
}
