<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Holiday;
use App\Model\RequestNumber;
use App\Model\Indexing;
use App\Classes\TatCalculator;
use App\Helpers;
use DateTime,Session;

class IndexingController extends Controller
{
    public function index(){
       //$indexings = Indexing::orderBy('priority_id')->orderBy('mail_received_time')->get();
        $indexingQuery = Indexing::leftJoin('process_queue','indexing.id', '=', 'process_queue.indexing_id');
        $indexingQuery->leftJoin('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id');
     
        $indexingQuery->leftJoin('mst_status as aq_status','aq_status.id', '=', 'audit_queue.audit_status_id');

        $indexingQuery->leftJoin('mst_status as pq_status','pq_status.id', '=', 'process_queue.status_id');
        $indexingQuery->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id');
        $indexingQuery->leftJoin('mst_priority_type','mst_priority_type.id', '=', 'indexing.priority_id');
        $indexingQuery->leftJoin('mst_request_type','mst_request_type.id', '=', 'indexing.request_type_id');
        $indexingQuery->leftJoin('mst_office','mst_office.id', '=', 'indexing.office_id');
        $indexingQuery->select('indexing.*','mst_region.name as region_name','mst_priority_type.name as priority_type','mst_request_type.name as request_type','mst_office.office_name as office_name');

        $indexingQuery->where(function ($indexingQuery) {
            $indexingQuery->whereNotIn('aq_status.status_name',['sent to pricer','sent to customer','sent to inside sales','done','disregard']);
            $indexingQuery->orWhereNull('aq_status.status_name');
            $indexingQuery->whereNotIn('pq_status.status_name',['sent to pricer','done','disregard']);
            $indexingQuery->orWhereNull('pq_status.status_name');
        });
        
        
        $indexingQuery->orderBy('indexing.priority_id');
        $indexingQuery->orderBy('indexing.mail_received_time');
        $indexings = $indexingQuery->get();
        return view('indexing.indexingView',compact('indexings'));
    }

    public function create(){
    	return view('indexing.indexingCreate');
    }

    public function store(Request $request){
       
        $indexing = new Indexing();
        $result = $indexing->validateIndexing($request->all());

        if ($result->fails()) {
            return redirect()->route('indexing.create')
                        ->withErrors($result)
                        ->withInput();
        }
        else{

            $saveResult = $indexing->saveIndexing($request->all());
            if($saveResult){
                Session::flash('message','RequestNo : <strong>'.$saveResult.'</strong> Added Successfully!');
                return redirect()->route('indexing.create');
            }
            else{

                Session::flash('error','Indexing Not Added');
                return redirect()->route('indexing.create');
            }
            
        }
    }

    public function edit($id)
    {
        $indexing = Indexing::findOrFail($id);
        $indexing->mail_received_time = date('m/d/Y H:i:s',strtotime($indexing->mail_received_time));
        return view('indexing.indexingEdit',compact('indexing'));
    }

    public function update(Request $request, $id)
    {
    	$indexing = new Indexing();
        $result = $indexing->validateIndexing($request->all());

        if ($result->fails()) {
            return redirect()->route('indexing.edit',$id)
                        ->withErrors($result)
                        ->withInput();
        }
        else{

            $saveIndexing = $indexing->updateIndexing($request->all(), $id);
            if($saveIndexing){
                Session::flash('message','Inexing Updated Successfully');
                return redirect()->route('indexing.edit',$id);
            }
            else{
                Session::flash('error','Inexing Not Updated');
                return redirect()->route('indexing.edit',$id);  
            }
            
        }
    }

    public function destroy($id)
    {
    	$indexing = Indexing::findOrFail($id);

        $indexing->delete();
                
        if($indexing){
            Session::flash('message','Indexing Deleted Successfully');
            return redirect()->route('indexing.index');
        }
        else{
            Session::flash('error','Indexing Not Deleted');
            return redirect()->route('indexing.index'); 
        }
    }
    
    public function requestNumber(Request $request)
    {
        if($request->date){
            $requestNumber = RequestNumber::create();
            /*$date = DateTime::createFromFormat('m/d/Y H:i:s', $request->date);
            $week = $date->format("W"); //week no
            $day = $date->format("d"); //day
            $month = $date->format("m"); //month*/
            $week = date('W', strtotime($request->date)); //week no
            $day = date('d', strtotime($request->date)); //day
            $month = date('m', strtotime($request->date)); //month
            $request_no['request_no'] = "WK$week/$day/$month#$requestNumber->id"; 
            //echo $date->format('Y-m-d H:i:s');
            echo json_encode($request_no);

        }
    }
}
