<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator, Schema;

class Status extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mst_status';
    public $columns = [];

    protected $rules = [
        'status_name'  => 'required|min:2|max:255',
    	'status_type'  => 'required',
    ];

    public function statusCat()
    {
        return $this->belongsTo('App\Model\StatusCat','statusCatId','id');

    }

    public function indexing()
    {
        return $this->belongsTo('App\Indexing');
    }

    public function validateStatus(array $data)
    {
    	
    	return  Validator::make($data, $this->rules);
    	
    }

    public function saveStatus(array $data, $id=null)
    {
    	//get all columns of current model

    	$columns = Schema::getColumnListing($this->table);

/****** automate save process getting all column names and text box name and save textbox value in columns ******/
    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}

    	}
        $this->status_name = strtolower($data['status_name']);
        $this->status_type = strtolower($data['status_type']);
    	return $this->save();
    }

    public function updateStatus(array $data, $id)
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
        $obj->status_name = strtolower($data['status_name']);
        $obj->status_type = strtolower($data['status_type']);
       
    	return $obj->save();
    }




}


