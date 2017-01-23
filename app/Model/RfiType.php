<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator, Schema;

class RfiType extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mst_rfi_type';
    public $columns = [];

    protected $rules = [
    	'rfi_type_name'  => 'required|min:2|max:255',
    ];

    
    public function validateRfiType(array $data)
    {
    	
    	return  Validator::make($data, $this->rules);
    	
    }

    public function saveRfiType(array $data, $id=null)
    {
    	//get all columns of current model

    	$columns = Schema::getColumnListing($this->table);

/****** automate save process getting all column names and text box name and save textbox value in columns ******/
    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}
    	}
        
    	return $this->save();
    }

    public function updateRfiType(array $data, $id)
    {
    	//get all columns of current model

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

