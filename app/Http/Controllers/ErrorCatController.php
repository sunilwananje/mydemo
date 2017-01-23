<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\ErrorCat;
use Session;


class ErrorCatController extends Controller
{
    public function index()
    {
    	return view('masters.errorCat.errorCatView');
    }

    public function create()
    {
    	return view('masters.errorCat.errorCatCreate');
    }

    public function edit($id)
    {
    	$recId = errorCat::findOrFail($id);
    	//dd($recId);
    	return view('masters.errorCat.errorCatEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$errorCat = new ErrorCat();
    	$res = $errorCat->validateErrorCat($req->all());

    	if ($res->fails()) {
            return redirect()->route('errorCat.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $errorCat->saveErrorCat($req->all());
        	if($saveYN){
        		Session::flash('message','Error Category Added Successfully');
        		return redirect()->route('errorCat.index');
        	}
        	else{

        		Session::flash('error','Error Category Not Added');
        		return redirect()->route('errorCat.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$errorCat = new ErrorCat();
    	$res = $errorCat->validateErrorCat($req->all());

    	if ($res->fails()) {
            return redirect()->route('errorCat.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $errorCat->updateErrorCat($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Error Category Updated Successfully');
        		return redirect()->route('errorCat.index');
        	}
        	else{

        		Session::flash('error','Error Category Not Updated Successfully');
        		return redirect()->route('errorCat.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = ErrorCat::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Error Category Deleted Successfully');
    		return redirect()->route('errorCat.index');
    	}
    	else{
    		Session::flash('error','Error Category Not Deleted');
    		return redirect()->route('errorCat.index');	
    	}
    }
}


