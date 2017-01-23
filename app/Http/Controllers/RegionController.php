<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Region;
use Session;


class RegionController extends Controller
{
    public function index()
    {
    	return view('masters.region.regionView');
    }

    public function create()
    {
    	return view('masters.region.regionCreate');
    }

    public function edit($id)
    {
    	$recId = region::findOrFail($id);
    	//dd($recId);
    	return view('masters.region.regionEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$region = new Region();
    	$res = $region->validateRegion($req->all());

    	if ($res->fails()) {
            return redirect()->route('region.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $region->saveRegion($req->all());
        	if($saveYN){
        		Session::flash('message','Region Added Successfully');
        		return redirect()->route('region.index');
        	}
        	else{

        		Session::flash('error','Region Not Added');
        		return redirect()->route('region.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$region = new Region();
    	$res = $region->validateRegion($req->all());

    	if ($res->fails()) {
            return redirect()->route('region.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $region->updateRegion($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Region Updated Successfully');
        		return redirect()->route('region.index');
        	}
        	else{

        		Session::flash('error','Region Not Updated');
        		return redirect()->route('region.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = Region::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Region Deleted Successfully');
    		return redirect()->route('region.index');
    	}
    	else{
    		Session::flash('error','Region Not Deleted');
    		return redirect()->route('region.index');	
    	}
    }
}
