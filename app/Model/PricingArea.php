<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator, Schema;

class PricingArea extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mst_pricing_area';
    public $columns = [];

    protected $rules = [
    	'name'  => 'required|min:2|max:255',
    ];

    public function validatePricingArea(array $data)
    {
    	
    	return  Validator::make($data, $this->rules);
    	
    }

    public function savePricingArea(array $data, $id=null)
    {

    	$columns = Schema::getColumnListing($this->table);
    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}
    	}
        
    	return $this->save();
    }

    public function updatePricingArea(array $data, $id)
    {

    	$columns = Schema::getColumnListing($this->table);

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
