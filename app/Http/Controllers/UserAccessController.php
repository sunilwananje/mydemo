<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\User;
use App\Model\Permission;
use App\Model\UserPermission;
use App\Model\Menu;
use Session,Route;


class UserAccessController extends Controller
{
    public $permissionName;

    public function index()
    {
    	return view('masters.userAccess.userAccessView');
    }

    public function create()
    {
        $menuData = Menu::select('id','menu_name')->where('parent_id','=',0)->get();
        
        return view('masters.userAccess.userAccessCreate',compact('menuData'));
    }

    public function edit($id)
    {
    	$recId = User::findOrFail($id);
        $menuData = Menu::select('id','menu_name')->where('parent_id','=',0)->get();

    	$perData = UserPermission::select('permission_id')->where('user_id','=',$id)->get()->toArray();
        $menuArray = array_column($perData, 'permission_id');
    	return view('masters.userAccess.userAccessEdit',compact('recId','menuData','menuArray'));
    }

    public function store(Request $req)
    {
    	$userAccess = new User();
    	$res = $userAccess->validateUserAccess($req->all());
    	if ($res->fails()) {
            return redirect()->route('userAccess.create')
                        ->withErrors($res)
                        ->withInput();
        }else{

        	$saveYN = $userAccess->saveUserAccess($req->all());

        	if($saveYN){
        		Session::flash('message','User Access Details Saved Successfully');
        		return redirect()->route('userAccess.index');
        	}
        	else{

        		Session::flash('error','User Access Details Not Saved');
        		return redirect()->route('userAccess.index');
        	}
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$userAccess = new User();
    	$res = $userAccess->validateUserAccess($req->all(), $id);
         //dd($res);
    	if ($res->fails()) {
            return redirect()->route('userAccess.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $userAccess->updateUserAccess($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','User Access Details Updated Successfully');
        		return redirect()->route('userAccess.index');
        	}
        	else{

        		Session::flash('error','User Access Details Not Updated');
        		return redirect()->route('userAccess.edit',$id);	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = User::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','User Access Deleted');
    		return redirect()->route('userAccess.index');
    	}
    	else{
    		Session::flash('error','User Access Not Deleted');
    		return redirect()->route('userAccess.index');	
    	}
    }

    public function syncRolePermission() {
        //dd(Route::getRoutes());
        foreach (Route::getRoutes() as $value) {
            //echo $value->getName()."-->";
            $this->permissionName = $value->getName();
            if($value->getName()){
                if (!$this->permissionDetails()) {
                    $displayName = ucwords(strtolower(str_replace(".", " ", $value->getName())));
                    $permissions = new Permission();
                    $permissions->name = $value->getName();
                    $permissions->display_name = $displayName;
                    $permissions->save();
                }  
            }
            
        }
        return redirect()->back();//'<h3>All Permissions Added...</h3>';
    }
    public function permissionDetails() {
        $query = Permission::orderBy('name', 'Asc');
        if ($this->permissionName) {
            $query->where('name', $this->permissionName);
            $result = $query->first();
        }else {
            $result = $query->get();
        }
        return $result;
    }
}

