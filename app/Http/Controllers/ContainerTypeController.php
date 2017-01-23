<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\ContainerType;
use Session;


class ContainerTypeController extends Controller
{
    public function index()
    {
    	return view('masters.containerType.containerTypeView');
    }

    public function create()
    {
    	return view('masters.containerType.containerTypeCreate');
    }

    public function edit($id)
    {
    	$recId = containerType::findOrFail($id);
    	//dd($recId);
    	return view('masters.containerType.containerTypeEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$containerType = new ContainerType();
    	$res = $containerType->validateContainerType($req->all());

    	if ($res->fails()) {
            return redirect()->route('containerType.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $containerType->saveContainerType($req->all());
        	if($saveYN){
        		Session::flash('message','Container Type Added Successfully');
        		return redirect()->route('containerType.index');
        	}
        	else{

        		Session::flash('error','Container Type Not Added');
        		return redirect()->route('containerType.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$containerType = new ContainerType();
    	$res = $containerType->validateContainerType($req->all());

    	if ($res->fails()) {
            return redirect()->route('containerType.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $containerType->updateContainerType($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Container Type Updated Successfully');
        		return redirect()->route('containerType.index');
        	}
        	else{

        		Session::flash('error','Container Type Not Updated');
        		return redirect()->route('containerType.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = ContainerType::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Container Type Deleted Successfully');
    		return redirect()->route('containerType.index');
    	}
    	else{
    		Session::flash('error','Container Type Not Deletd');
    		return redirect()->route('containerType.index');	
    	}
    }
}

