<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\PriorityType;
use Session;


class PriorityTypeController extends Controller
{
    public function index()
    {
    	return view('masters.priorityType.priorityTypeView');
    }

    public function create()
    {
    	return view('masters.priorityType.priorityTypeCreate');
    }

    public function edit($id)
    {
    	$recId = priorityType::findOrFail($id);
    	//dd($recId);
    	return view('masters.priorityType.priorityTypeEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$priorityType = new PriorityType();
    	$res = $priorityType->validatePriorityType($req->all());

    	if ($res->fails()) {
            return redirect()->route('priorityType.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $priorityType->savePriorityType($req->all());
        	if($saveYN){
        		Session::flash('message','Priority Type Added Successfully');
        		return redirect()->route('priorityType.index');
        	}
        	else{

        		Session::flash('error','Priority Type Not Added');
        		return redirect()->route('priorityType.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$priorityType = new PriorityType();
    	$res = $priorityType->validatePriorityType($req->all());

    	if ($res->fails()) {
            return redirect()->route('priorityType.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $priorityType->updatePriorityType($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Priority Type Updated Successfully');
        		return redirect()->route('priorityType.index');
        	}
        	else{

        		Session::flash('error','Priority Type Not Updated');
        		return redirect()->route('priorityType.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = PriorityType::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Priority Type Deleted Successfully');
    		return redirect()->route('priorityType.index');
    	}
    	else{
    		Session::flash('error','Priority Type Not Deleted');
    		return redirect()->route('priorityType.index');	
    	}
    }
}

