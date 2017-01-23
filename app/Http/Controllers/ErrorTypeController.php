<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\ErrorType;
use Session;


class ErrorTypeController extends Controller
{
    public function index()
    {
    	return view('masters.errorType.errorTypeView');
    }

    public function create()
    {
    	return view('masters.errorType.errorTypeCreate');
    }

    public function edit($id)
    {
    	$recId = errorType::findOrFail($id);
    	//dd($recId);
    	return view('masters.errorType.errorTypeEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$errorType = new ErrorType();
    	$res = $errorType->validateErrorType($req->all());

    	if ($res->fails()) {
            return redirect()->route('errorType.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $errorType->saveErrorType($req->all());
        	if($saveYN){
        		Session::flash('message','Error Type Added Successfully');
        		return redirect()->route('errorType.index');
        	}
        	else{

        		Session::flash('error','Error Type Not Added');
        		return redirect()->route('errorType.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$errorType = new ErrorType();
    	$res = $errorType->validateErrorType($req->all());

    	if ($res->fails()) {
            return redirect()->route('errorType.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $errorType->updateErrorType($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Error Type Updated Successfully');
        		return redirect()->route('errorType.index');
        	}
        	else{

        		Session::flash('error','Error Type Not Updated Successfully');
        		return redirect()->route('errorType.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = ErrorType::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Error Type Deleted Successfully');
    		return redirect()->route('errorType.index');
    	}
    	else{
    		Session::flash('error','Error Type Not Deleted');
    		return redirect()->route('errorType.index');	
    	}
    }
}


