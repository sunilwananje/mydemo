<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator, Schema;
use App\Model\UserPermission;
use Session,DB;
class User extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'users';
    public $columns = [];

    protected $rules = [
     	'name'    => 'required|min:2|max:255',
     	'username' => 'required|unique:users,username',
     	'role_id'  => 'required',
        'email'   => 'required|email|unique:users,email'
    ];

    
    public function role()
    {
        return $this->belongsTo('App\Model\Role','role_id','id');
    }

    public function validateUserAccess(array $data, $id=null)
    {
    	if($id)
        $this->rules = [
            'name'    => 'required|min:2|max:255',
            'username' => 'required|unique:users,username,'.$id,
            'role_id'  => 'required',
            'email'   => 'required|email|unique:users,email,'.$id
        ];            
    	return  Validator::make($data, $this->rules);
    	
    }

    public function saveUserAccess(array $data, $id=null)
    {
    	//get all columns of current userAccessl

    	$columns = Schema::getColumnListing($this->table);
    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$this->$key = $value; 
    		}
    	}
        $result = $this->save();
        //dd($result);
        if($result)
            $this->insertUsersPermission($data['users_permission'],$this->id);


    	return $result;
    }

    public function updateUserAccess(array $data, $id)
    {
    	//get all columns of current userAccessl

    	$columns = Schema::getColumnListing($this->table);

    	$obj = $this->find($id);

    	foreach ($data as $key => $value) {
    		if(in_array($key, $columns)){
    			$obj->$key = $value;
    		}
    	}
        $result = $obj->save();
        if($result){
            $delete = UserPermission::where('user_id', '=', $id)->delete();
            $this->insertUsersPermission($data['users_permission'],$id);
        }
             

    	return $obj->save();
    }

    public function insertUsersPermission($data, $id)
    {
        //dd($data);
        //get all columns of current userAccessl
        foreach($data as $value){
            $permission = new UserPermission();
            $permission->permission_id = $value;
            $permission->user_id = $id;
            $result = $permission->save();
        }
        /*$perData = DB::table('mst_permission_users as pu')
                         ->join('mst_permissions as p', 'p.id', '=', 'pu.permission_id')
                         ->where('pu.user_id','=',$id)
                         ->select('p.name')
                         ->get();
        $permissionArray = array_column($perData, 'name');
        Session::set('permissions',$permissionArray);*/
        return $result;
    }

}


