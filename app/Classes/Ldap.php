<?php
namespace App\Classes;

use App\Model\User;
use App\Model\UserPermission;
use App\Model\Role;
use App\Model\UserLogged;
use Session,Validator,DB;

class Ldap 
{
  public $flag = false;
  
  public function ldapValidation($data){
     $rules=array( 'username' => 'required|min:5|max:255',
                   'password' => 'required',
                  );
     return $valid = Validator::make($data->all(), $rules);
  }

  public function checkLdap($username,$password)
  {

      $password = decrypt($password);

  	  $ldaprdn="inchessc\\".$username;
  	  $ldapconn=ldap_connect('10.13.60.7') ;
      if ($ldapconn){
        	$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $password);
          
          $unauth =  User::where('username', $username)->first();
          
        	if (($ldapbind) && ($password != '') && $unauth) {

            $perData = DB::table('mst_permission_users as pu')
                         ->join('mst_permissions as p', 'p.id', '=', 'pu.permission_id')
                         ->where('pu.user_id','=',$unauth->id)
                         ->select('p.name')
                         ->get();

            foreach($perData as $k=>$v) {
                $permissionArray[$k] = $v->name;
            }
            Session::put('ldap','access');
        		Session::put('ldap_username',$username);
            Session::put('ldap_name',$unauth->name);
            Session::put('user_id',$unauth->id);
            Session::put('office_id',$unauth->office_id);
            Session::put('user_role',$unauth->role->name);
            Session::put('permissions',$permissionArray);
            $this->logInTime($unauth->id);
            $this->flag = true;
        	} else {
            Session::forget('ldap');
            Session::forget('ldap_username');
            Session::forget('ldap_name');
            Session::forget('user_id');
        		Session::forget('user_role');
            $this->flag = false;
        	}
      }
      ldap_close($ldapconn);
      return $this->flag ;
    }

    public function logInTime($id)
    {
      date_default_timezone_set(TIME_ZONE);
      $userLog = new UserLogged();

      $currTime = time();
      $startTime = date('Y-m-d 00:00:00');
      $endTime = date('Y-m-d 07:00:00');

      if($currTime >= $startTime && $currTime <=$endTime)
          $logInDate = date('Y-m-d',strtotime("-1 day", $currTime));
      else
          $logInDate = date('Y-m-d');

      $user_log_in = UserLogged::whereRaw("DATE(login_time) = '$logInDate'")->where('user_id', $id)->first();
      if($user_log_in){
          return true;
      }
      else{
        $userLog->user_id = $id;            
        $userLog->login_time = date('Y-m-d H:i:s');;            
        $userLog->save();
        return true;
      }
    }
}
