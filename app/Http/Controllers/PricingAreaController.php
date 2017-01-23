<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\PricingArea;
use Session;

class PricingAreaController extends Controller
{
    public function index()
    {
    	return view('masters.pricingArea.pricingAreaView');
    }

    public function create()
    {
    	return view('masters.pricingArea.pricingAreaCreate');
    }

    public function edit($id)
    {
    	$recId = PricingArea::findOrFail($id);
    	//dd($recId);
    	return view('masters.pricingArea.pricingAreaEdit',compact('recId'));
    }

    public function store(Request $req)
    {
    	$pricing_area = new PricingArea();
    	$res = $pricing_area->validatePricingArea($req->all());

    	if ($res->fails()) {
            return redirect()->route('pricingArea.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $pricing_area->savePricingArea($req->all());
        	if($saveYN){
        		Session::flash('message','Pricing Area Added Successfully');
        		return redirect()->route('pricingArea.index');
        	}
        	else{

        		Session::flash('error','Pricing Area Not Added');
        		return redirect()->route('pricingArea.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$pricing_area = new PricingArea();
    	$res = $pricing_area->validatePricingArea($req->all());

    	if ($res->fails()) {
            return redirect()->route('pricingArea.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $pricing_area->updatePricingArea($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Pricing Area Updated Successfully');
        		return redirect()->route('pricingArea.index');
        	}
        	else{

        		Session::flash('error','Pricing Area Not Updated');
        		return redirect()->route('pricingArea.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = PricingArea::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Pricing Area Deleted Successfully');
    		return redirect()->route('pricingArea.index');
    	}
    	else{
    		Session::flash('error','Pricing Area Not Deleted');
    		return redirect()->route('pricingArea.index');	
    	}
    }
}
