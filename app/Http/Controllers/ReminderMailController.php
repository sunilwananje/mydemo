<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\ReminderMailSetting;
use Session;

class ReminderMailController extends Controller
{
    public function index()
    {
    	return view('masters.reminder.mailSettingView');
    }

    public function create()
    {
    	return view('masters.reminder.mailSettingCreate');
    }

    public function edit($id)
    {
    	$email = ReminderMailSetting::findOrFail($id);
    	//dd($recId);
    	return view('masters.reminder.mailSettingEdit',compact('email'));
    }

    public function store(Request $req)
    {
    	$mode = new ReminderMailSetting();
    	$res = $mode->validateEmail($req->all());

    	if ($res->fails()) {
            return redirect()->route('reminder.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $mode->saveEmail($req->all());
        	if($saveYN){
        		Session::flash('message','Reminder Email Id Added Successfully');
        		return redirect()->route('reminder.index');
        	}
        	else{

        		Session::flash('error','Reminder Email Id Not Added');
        		return redirect()->route('reminder.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$mode = new ReminderMailSetting();
    	$res = $mode->validateEmail($req->all());

    	if ($res->fails()) {
            return redirect()->route('reminder.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $mode->updateEmail($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Reminder Email Id Updated Successfully');
        		return redirect()->route('reminder.index');
        	}
        	else{

        		Session::flash('error','Reminder Email Id Not Updated');
        		return redirect()->route('reminder.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = ReminderMailSetting::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Reminder Email Id Deleted Successfully');
    		return redirect()->route('reminder.index');
    	}
    	else{
    		Session::flash('error','Reminder Email Id Not Deleted');
    		return redirect()->route('reminder.index');	
    	}
    }
}
