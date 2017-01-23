<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator, Schema;

class Office extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mst_office';
    public $columns = [];

    protected $rules = [
    'office_name'     => 'required|min:2|max:255',
    'office_address'  => 'required'
    ];

    public function validateOffice(array $data)
    {
    	return  Validator::make($data, $this->rules);
    }

    public function saveOffice(array $data, $id=null)
    {
    	$columns = Schema::getColumnListing($this->table);//get all columns of current model
    	
    	foreach ($data as $key => $value) { //$key is common for column name and input field name
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}
    	}
        
    	return $this->save();
    }

    public function updateOffice(array $data, $id)
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




}
