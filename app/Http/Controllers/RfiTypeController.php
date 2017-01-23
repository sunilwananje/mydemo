<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\RfiType;
use Session;

class RfiTypeController extends Controller
{
    public function index()
    {
    	return view('masters.rfiType.rfiTypeView');
    }

    public function create()
    {
    	return view('masters.rfiType.rfiTypeCreate');
    }

    public function edit($id)
    {
    	$recId = rfiType::findOrFail($id);
    	//dd($recId);
    	return view('masters.rfiType.rfiTypeEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$rfiType = new RfiType();
    	$res = $rfiType->validateRfiType($req->all());

    	if ($res->fails()) {
            return redirect()->route('rfi.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $rfiType->saveRfiType($req->all());
        	if($saveYN){
        		Session::flash('message','RFI Type Saved Successfully');
        		return redirect()->route('rfi.index');
        	}
        	else{

        		Session::flash('error','RFI Type Not Saved');
        		return redirect()->route('rfi.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$rfiType = new RfiType();
    	$res = $rfiType->validateRfiType($req->all());

    	if ($res->fails()) {
            return redirect()->route('rfi.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $rfiType->updateRfiType($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','RFI Type Updated Successfully');
        		return redirect()->route('rfi.index');
        	}
        	else{

        		Session::flash('error','RFI Type Not Updated');
        		return redirect()->route('rfi.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = RfiType::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','RFI Type Deleted');
    		return redirect()->route('rfi.index');
    	}
    	else{
    		Session::flash('error','RFI Type Not Deleted');
    		return redirect()->route('rfi.index');	
    	}
    }
}
