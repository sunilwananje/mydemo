<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\AuditingQueue;
use App\Model\ProcessQueue;
use App\Model\User;
use Validator, Schema,Session;

class Pricer extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'pricer';

    public $columns = [];

    protected $rules = [
    'pol_port'     => 'required',
    'pol_region'     => 'required',
    'pod_port'     => 'required',
    'pod_region'     => 'required',
    'pricer_name'     => 'required|min:2|max:255',
    ];

    public function validatePricer(array $data)
    {
        return  Validator::make($data, $this->rules);
    }

    public function savePricer(array $data, $id=null)
    {
        $columns = Schema::getColumnListing($this->table);//get all columns of current model
        
        foreach ($data as $key => $value) { //$key is common for column name and input field name
            if(in_array($key, $columns) && !empty($value)){
                $this->$key = $value;
            }
        }
        $this->updated_by = Session::get('user_id');
        if($data['audit_queue_id']){
            $audit = AuditingQueue::where('id', $data['audit_queue_id']) //update status to audit queue
              ->update(['audit_status_id' => $data['status']]);
        }else{
            $audit = ProcessQueue::where('id', $data['process_queue_id']) //update status to audit queue
              ->update(['status_id' => $data['status']]);
        }
        
        
        return $this->save();
    }

    public function updatePricer(array $data, $id)
    {
        $columns = Schema::getColumnListing($this->table);//get all columns of current model

        $obj = $this->find($id);

        foreach ($data as $key => $value) { //$key is common for column name and input field name
            if(in_array($key, $columns)){
                $obj->$key = $value;
            }
        }
        
        return $obj->save();
    }
    public function getPricer()
    {//get pricer table data
        $query = Pricer::select('pricer.*','users.name as updated_user','process_queue.sq_no')
                        ->join('users','pricer.updated_by','=','users.id')
                        ->join('process_queue','pricer.process_queue_id','=','process_queue.id');
        $result = $query->get();
        return $result;
    }
    
}
