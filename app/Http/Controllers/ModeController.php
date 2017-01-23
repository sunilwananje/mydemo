<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Mode;
use Session;


class ModeController extends Controller
{
    public function index()
    {
    	return view('masters.mode.modeView');
    }

    public function create()
    {
    	return view('masters.mode.modeCreate');
    }

    public function edit($id)
    {
    	$recId = mode::findOrFail($id);
    	//dd($recId);
    	return view('masters.mode.modeEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$mode = new Mode();
    	$res = $mode->validateMode($req->all());

    	if ($res->fails()) {
            return redirect()->route('mode.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $mode->saveMode($req->all());
        	if($saveYN){
        		Session::flash('message','Mode Added Successfully');
        		return redirect()->route('mode.index');
        	}
        	else{

        		Session::flash('error','Mode Not Added');
        		return redirect()->route('mode.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$mode = new Mode();
    	$res = $mode->validateMode($req->all());

    	if ($res->fails()) {
            return redirect()->route('mode.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $mode->updateMode($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Mode Updated Successfully');
        		return redirect()->route('mode.index');
        	}
        	else{

        		Session::flash('error','Mode Not Updated');
        		return redirect()->route('mode.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = Mode::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Mode Deleted Successfully');
    		return redirect()->route('mode.index');
    	}
    	else{
    		Session::flash('error','Mode Not Deleted');
    		return redirect()->route('mode.index');	
    	}
    }
}

