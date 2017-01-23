<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Permission;
use Validator,Schema;

class Menu extends Model
{
	protected $table = 'mst_menu';
	public $columns = [];

    protected $rules = [
    	'menu_name'  => 'required|min:2|max:255',
    	'parent_id'  => 'required',
    	//'permission_id'  => 'required',
    ];

	public function childMenu()
    {
        return $this->hasMany('App\Model\Menu','parent_id');
    }

    public function parentMenu()
    {
        return $this->belongsTo('App\Model\Menu','parent_id');
    }

    public function validateMenu(array $data)
    {
    	return  Validator::make($data, $this->rules);
    }

    public function saveMenu(array $data)
    {
    	$columns = Schema::getColumnListing($this->table);   
    	    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value;
    		}

    	}

    	return $this->save();
    }

    public function updateMenu(array $data,$id)
    {
    	$columns = Schema::getColumnListing($this->table);
    	$obj = $this->find($id);    	
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$obj->$key = $value;
    		}

    	}
    	return $obj->save();
    }
}