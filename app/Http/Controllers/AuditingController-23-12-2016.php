<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Indexing;
use App\Model\ProcessQueue;
use App\Model\Status;
use App\Model\AuditingQueue;
use App\Model\User;
use App\Model\RequestType;

use Session,Input,Response;

class AuditingController extends Controller
{
    public function index(){
        $auditing = new AuditingQueue();
        $auditings = $auditing->auditingList();
		return view('auditing.auditingView',compact('auditings'));
    }
    public function create(){
      return view('indexing.publishingCreate');
    }
    public function show(Request $request, $id)
    {
    	date_default_timezone_set(TIME_ZONE);
    	
        $auditing = AuditingQueue::find($id);
    	
    	if(empty($auditing->audit_by)){
    		$auditing->audit_start_date = date('Y-m-d H:i:s');
    	}
    	   

    	 $auditing->audit_by = Session::get('user_id');


        if(!$auditing->audit_status_id){
        	$status = Status::select('id')->where('status_name','in process')
	    	                              ->where('status_type','apf')
	    	                              ->first();
	    	$auditing->audit_status_id = $status->id;
        }
	    	
        $auditing->save();

        /*$auditing = new AuditingQueue();
        $auditings = $auditing->auditingList($id);
        dd($auditings);*/

        $publishings = ProcessQueue::find($auditing->process_queue_id);
        $auditing->publishing = $publishings;
        $indexings = Indexing::find($publishings->indexing_id);
        $user = User::find($publishings->publish_by);
        $requestType = RequestType::find($indexings->request_type_id);
        $auditing->indexing = $indexings;
        $auditing->request_type_name = $requestType->name;
        $auditing->publisher_name = $user->name;
        $auditing->action = $request->action;
        //return view('auditing.auditingCreate',compact('auditing'));
    	return view('auditing.apfCreateModal',compact('auditing'));
    }
    public function store(Request $request){
        $auditing = new AuditingQueue();
    	$res = $auditing->validateAPF($request->all());
         
    	if ($res->fails()) {
    		
            return redirect()->route('auditing.show', $request->auditing_id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	//$result = ProcessQueue::where('indexing_id',$data['indexing_id'])->->exists();

        	$saveYN = $auditing->saveAPF($request->all());

        	if($saveYN){
        		Session::flash('message','APF Saved Successfully');
        		return redirect()->route('auditing.index');
        	}
        	else{

        		Session::flash('error','APF Not Saved');
        		return redirect()->route('auditing.show', $request->auditing_id);
        	}
        	
        }
    }
    public function edit(Request $request,$id)
    {
    	date_default_timezone_set(TIME_ZONE);
        $auditing = AuditingQueue::find($id);
        $publishings = ProcessQueue::find($auditing->process_queue_id);
        $auditing->publishing = $publishings;
        $indexings = Indexing::find($publishings->indexing_id);
        $user = User::find($publishings->publish_by);
        $requestType = RequestType::find($indexings->request_type_id);
        $auditing->indexing = $indexings;
        $auditing->request_type_name = $requestType->name;
        $auditing->publisher_name = $user->name;
        $auditing->action = $request->action;
        if(isset($request->rfi_id)){
          $auditing->rfi_id = $request->rfi_id;
        }
        //return view('auditing.auditingEdit',compact('auditing'));
        if($auditing->audit_by != Session::get('user_id') && Session::get('user_role')!='admin'){
          return view('auditing.errorModal');
        }
        //return view('auditing.errorModal');
        return view('auditing.apfEditModal',compact('auditing'));
    }

    /*Update Audit Process Form*/
    public function update(Request $request, $id)
    {
    	$publishing = new AuditingQueue();
        $resopnseArray = [];
    	$res = $publishing->validateAPF($request->all(),$id); // get validate
    	if ($res->fails()) {
            $messages = $res->messages();
            $msgArray = $messages->toArray();
            
            foreach ($msgArray as $key => $val) {
                $resopnseArray[$key . '_err'] = $messages->first($key);
            }
            $resopnseArray['errors'] = 1;
            //return redirect()->route('auditing.edit',$id)->withErrors($res)->withInput();
        }
        else{

        	$saveYN = $publishing->updateAPF($request->all(), $id); //
            $resopnseArray['success'] = 'done';
            $resopnseArray['url'] = route('auditing.index');
        	if($saveYN){
                Session::flash('message','Audit Process Form Updated');
        	}
        	else{
        		Session::flash('error','Audit Process Form Not Updated');
        	}
         //return redirect()->route('auditing.index');
        }

        return Response::json($resopnseArray, 200)->header('Content-Type', 'application/json');
    }
    public function destroy($id)
    {
    }

     public function ootEnable(Request $request){
       $auditing = AuditingQueue::find($request->id);
       $request->oot_status=1;
       $auditing->oot = $request->oot_status;
       $auditing->save();
       $msg='';
       if($request->oot_status==0){
        $msg = "OOT Not Applicable";
       }elseif($request->oot_status==1){
        $msg = "OOT Enabled Successfully";
       }elseif($request->oot_status==2){
        $msg = "OOT Disabled Successfully";
       }
       Session::flash('message',$msg);
            return redirect()->route('auditing.index');
    }
}
