<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Tat;
use Session;


class TatController extends Controller
{
    public function index()
    {
    	return view('masters.tat.tatView');
    }

    public function create()
    {
    	return view('masters.tat.tatCreate');
    }

    public function edit($id)
    {
    	$recId = tat::findOrFail($id);
    	//dd($recId);
    	return view('masters.tat.tatEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$tat = new Tat();
    	$res = $tat->validateTat($req->all());

    	if ($res->fails()) {
            return redirect()->route('tat.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $tat->saveTat($req->all());
        	if($saveYN){
        		Session::flash('message','TAT Saved Successfully');
        		return redirect()->route('tat.index');
        	}
        	else{

        		Session::flash('error','TAT Not Saved');
        		return redirect()->route('tat.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$tat = new Tat();
    	$res = $tat->validateTat($req->all());

    	if ($res->fails()) {
            return redirect()->route('tat.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $tat->updateTat($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','TAT Updated Successfully');
        		return redirect()->route('tat.index');
        	}
        	else{

        		Session::flash('error','TAT Not Updated');
        		return redirect()->route('tat.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = Tat::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','TAT Deleted Successfully');
    		return redirect()->route('tat.index');
    	}
    	else{
    		Session::flash('error','TAT Not Deleted');
    		return redirect()->route('tat.index');	
    	}
    }
}


