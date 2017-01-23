<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator,Session,Schema;

class Holiday extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mst_holiday';
    public $columns = [];

    protected $rules = [
    	'name'  => 'required|min:2|max:255',
    	'holiday_date'  => 'required|date|date_format:d-m-Y',
    	'office_id'  => 'required',
    ];

    public function validateHoliday(array $data)
    {
    	return  Validator::make($data, $this->rules);
    }

    public function saveHoliday(array $data)
    {
    	$columns = Schema::getColumnListing($this->table);   
    	    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}

    	}
        $this->holiday_date = date('Y-m-d',strtotime($data['holiday_date']));
    	return $this->save();
    }

    public function updateHoliday(array $data,$id)
    {
    	$columns = Schema::getColumnListing($this->table);
    	$obj = $this->find($id);    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$obj->$key = $value;
    		}

    	}
        $obj->holiday_date = date('Y-m-d',strtotime($data['holiday_date']));
    	return $obj->save();
    }

    public function office()
    {
        return $this->belongsTo('App\Model\Office');
    }

}
