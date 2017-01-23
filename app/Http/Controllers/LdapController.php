<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Classes\Ldap;
use Session,Redirect;

class LdapController extends Controller
{
    public function checkLogin(Request $request)
    {
    	$ldap = new Ldap();

    	$validationResult = $ldap->ldapValidation($request);

    	if($validationResult->fails()){
        	return redirect('/login')
                    ->withErrors($validationResult)
                    ->withInput();
   		}
        $pwd = encrypt($request->password);
        //dd($pwd);
        $result = $ldap->checkLdap($request->username,$pwd);
        //$result = $ldap->checkLdap($request->username,$request->password);

        if($result)
          // return redirect()->intended('/dashboard');
            return redirect('/dashboard');
           
        return redirect('/login')->with('login_status', 'Invalid user credentials!');
    
    }
    
    public function logout(){
    	Session::flush();
    	return redirect('/login');
    }
}
