<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator, Schema;

class Tat extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mst_tat';
    public $columns = [];

    protected $rules = [
     	'priority_id' => 'required',
    	'tat_time'  => 'required|numeric'
    ];

    public function priority()
    {
    	return $this->hasOne('App\PriorityType','priority_type_id');

    }

    public function validateTat(array $data)
    {
    	
    	return  Validator::make($data, $this->rules);
    	
    }

    public function saveTat(array $data, $id=null)
    {
    	//get all columns of current tatl

    	$columns = Schema::getColumnListing($this->table);

/****** automate save process getting all column names and text box name and save textbox value in columns ******/
    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}
    	}
        
    	return $this->save();
    }

    public function updateTat(array $data, $id)
    {
    	//get all columns of current tatl

    	$columns = Schema::getColumnListing($this->table);

/****** automate save process getting all column names and text box name and save textbox value in columns ******/
    	$obj = $this->find($id);
    	//dd($obj);
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$obj->$key = $value;
    		}
    	}
        
    	return $obj->save();
    }




}


