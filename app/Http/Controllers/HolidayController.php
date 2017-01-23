<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Holiday;
use Session;

class HolidayController extends Controller
{
    public function index()
    {
    	return view('masters.holiday.viewHoliday');
    }

    public function create()
    {
    	return view('masters.holiday.holidayCreate');
    }

    public function edit($id)
    {
    	$holidayData = Holiday::findOrFail($id);
    	//dd($holidayData);
    	return view('masters.holiday.holidayEdit',compact('holidayData'));
    }

    public function store(Request $req)
    {
    	$holiday = new Holiday();
    	$res = $holiday->validateHoliday($req->all());

    	if ($res->fails()) {
            return redirect()->route('holiday.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $holiday->saveHoliday($req->all());
        	if($saveYN){
        		Session::flash('message','Holiday Added Successfully');
        		return redirect()->route('holiday.index');
        	}
        	else{

        		Session::flash('error','Holiday Not Added');
        		return redirect()->route('holiday.index');
        	}
        	
        }
    	
    }

    public function update(Request $req, $id)
    {
    	
    	$holiday = new Holiday();
    	$res = $holiday->validateHoliday($req->all());

    	if ($res->fails()) {
            return redirect()->route('holiday.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $holiday->updateHoliday($req->all(), $id);
        	if($saveYN){

        		Session::flash('message','Holiday Updated Successfully');
        		return redirect()->route('holiday.index');
        	}
        	else{

        		Session::flash('error','Holiday Not Updated');
        		return redirect()->route('holiday.index');	
        	}
        	
        }
    }

    public function destroy($id)
    {
    	$recId = Holiday::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Holiday Deleted Successfully');
    		return redirect()->route('holiday.index');
    	}
    	else{
    		Session::flash('error','Holiday Not Deleted');
    		return redirect()->route('holiday.index');	
    	}
    }



}
