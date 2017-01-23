<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Office;
use Session;


class OfficeController extends Controller
{
    public function index()
    {
    	//$office = Office::pa();

    	return view('masters.office.viewOffice');
    }

    public function create()
    {
    	return view('masters.office.officeCreate');
    }

    public function edit($id)
    {
    	$recId = Office::findOrFail($id);
    	//dd($recId);
    	return view('masters.office.officeEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$office = new Office();
    	$res = $office->validateOffice($req->all());

    	if ($res->fails()) {
            return redirect('office/create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $office->saveOffice($req->all());
        	if($saveYN){
        		Session::flash('message','Office Added successfully');
        		return redirect()->route('office.index');
        	}
        	else{

        		Session::flash('error','Office Not Added');
        		return redirect()->route('office.index');	
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$office = new Office();
    	$res = $office->validateOffice($req->all());

    	if ($res->fails()) {
            return redirect()->route('office.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $office->updateOffice($req->all(), $id);
        	if($saveYN){
        		Session::flash('message','Office Updated successfully');
        		return redirect()->route('office.index');
        	}
        	else{

        		Session::flash('error','Office Not Updated');
        		return redirect()->route('office.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = Office::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
        		Session::flash('message','Office Deleted Successfully');
        		return redirect()->route('office.index');
    	}
    	else{

    		Session::flash('error','Office Not Deleted');
    		return redirect()->route('office.index');	
    	}
    }
}
