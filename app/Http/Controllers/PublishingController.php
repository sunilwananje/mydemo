<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Indexing;
use App\Model\ProcessQueue;
use App\Model\Status;
use App\Model\PartnerCodeDb;
use Session,Input,Response;

class PublishingController extends Controller
{
    public function index(){

    	$publishings = Indexing::leftJoin('process_queue', 'indexing.id', '=', 'process_queue.indexing_id')
    						->leftJoin('mst_modes','mst_modes.id', '=', 'process_queue.mode_id')
    						->leftJoin('mst_rfi_type','mst_rfi_type.id', '=', 'process_queue.rfi_type_id')
    						->leftJoin('mst_status','mst_status.id', '=', 'process_queue.status_id')
    						->leftJoin('mst_error_cat','mst_error_cat.id', '=', 'process_queue.error_cat_id')
    						->leftJoin('mst_error_type','mst_error_type.id', '=', 'process_queue.error_type_id')
    						->leftJoin('users','users.id', '=', 'process_queue.rfi_raised_by')
    						->leftJoin('users as err_user','err_user.id', '=', 'process_queue.error_done_by')
    						->leftJoin('users as publisher','publisher.id', '=', 'process_queue.publish_by')
                ->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id')
                ->leftJoin('mst_priority_type','mst_priority_type.id', '=', 'indexing.priority_id')

    						->select('process_queue.*','indexing.*','indexing.id as indexing_id', 'mst_rfi_type.rfi_type_name as rfi_name','mst_error_cat.name as err_cat_name', 'mst_error_type.name as err_type_name', 'users.name as rfi_by_name', 'err_user.name as error_done_by_name','publisher.name as publish_by_name','mst_status.status_name', 'process_queue.id as process_queue_id','mst_priority_type.name as priority_type','mst_region.name as region_name')

    						->orderBy('indexing.priority_id','DESC')
    						->orderBy('indexing.mail_received_time','ASC')
                ->whereIn('mst_status.status_name',['pending out','in process'])
    						//->whereNotIn('mst_status.status_name',['pending in','sent to audit','sent to pricer','done','disregard'])
    						//->where('mst_status.status_name','!=','send to audit')
                            //->where('mst_status.status_name','!=','done')
    						->orWhereNull('mst_status.status_name')
							->get();

							//dd($publishings);

        return view('publishing.publishingView',compact('publishings'));
        //return view('publishing.pv',compact('publishings'));
    }
    public function create(){
      return view('indexing.publishingCreate');
    }
    public function show(Request $request, $id)
    {
      //dd($request->action);
    	date_default_timezone_set(TIME_ZONE);
    	$status = Status::select('id')->where('status_name','in process')
                                      ->where('status_type','upf')
                                      ->first();
        $upf = ProcessQueue::where('indexing_id',$id)->first();
        if($upf){
         $upf_id = $upf->id;
        }else{
	    	$upf = new ProcessQueue();
	    	$upf->indexing_id = $id;
	    	$upf->publish_by = Session::get('user_id');
	    	$upf->publish_start_date = date('Y-m-d H:i:s');
	    	$upf->status_id = $status->id;
	        $upf->save();
	        $upf_id = $upf->id;
        }
        $indexing = Indexing::find($id);
        $indexing->publishing_id = $upf_id;
        $indexing->publishings = $upf;
        $indexing->action = $request->action;
        if(isset($request->rfi_id)){
          $indexing->rfi_id = $request->rfi_id;
        }
        
    	 //return view('publishing.publishingCreate',compact('indexing'));
        if($upf->publish_by != Session::get('user_id') && Session::get('user_role')!='admin'){
          return view('publishing.errorModal');
        }
        return view('publishing.upfModal',compact('indexing'));

    }
    public function store(Request $request){
      $publishing = new ProcessQueue();
    	$res = $publishing->validateUPF($request->all());
      $resopnseArray = [];

    	if ($res->fails()) {
    		    $messages = $res->messages();
            $msgArray = $messages->toArray();
            
            foreach ($msgArray as $key => $val) {
                $resopnseArray[$key . '_err'] = $messages->first($key);
            }
            $resopnseArray['errors'] = 1;
            //return redirect()->route('publishing.show', $request->indexing_id)->withErrors($res)->withInput();
        }
        else{

        	//$result = ProcessQueue::where('indexing_id',$data['indexing_id'])->->exists();

        	$saveYN = $publishing->saveUPF($request->all());
          $resopnseArray['success'] = 'done';
          $resopnseArray['url'] = route('publishing.index');

        	if($saveYN){
        		Session::flash('message','UPF Saved Successfully');
        		//return redirect()->route('publishing.index');
        	}
        	else{
        		Session::flash('error','UPF Not Saved');
        		//return redirect()->route('publishing.show', $request->indexing_id);
        	}
        	
        }

        return Response::json($resopnseArray, 200)->header('Content-Type', 'application/json');
    }
    public function edit($id)
    {
        
    }
    public function update(Request $request, $id)
    {
    	$publishing = new ProcessQueue();
    	$res = $publishing->validateUPF($request->all(),$id);
      $resopnseArray = [];

    	if ($res->fails()) {
            $messages = $res->messages();
            $msgArray = $messages->toArray();
            
            foreach ($msgArray as $key => $val) {
                $resopnseArray[$key . '_err'] = $messages->first($key);
            }
            $resopnseArray['errors'] = 1;
            //return redirect()->route('publishing.show',$request->indexing_id)->withErrors($res)->withInput();
        }
        else{

        	$saveYN = $publishing->updateUPF($request->all(), $id);
          $resopnseArray['success'] = 'done';
          $resopnseArray['url'] = route('publishing.index');
          
        	if($saveYN){

        		Session::flash('message','User Process Form Updated');
            //return redirect()->route('publishing.index'); 
        	    
        	}
        	else{

        		Session::flash('error','User Process Form Not Updated');
        		//return redirect()->route('publishing.index');	
        	}
        	
        }

        return Response::json($resopnseArray, 200)->header('Content-Type', 'application/json');
    }
    public function destroy($id)
    {
      $indexing = Indexing::findOrFail($id);

        $indexing->delete();
                
        if($indexing){
            Session::flash('message','Request Deleted Successfully');
            return redirect()->route('publishing.index');
        }
        else{
            Session::flash('error','Request Not Deleted');
            return redirect()->route('publishing.index'); 
        }
    }


    public function getAdminTracker(Request $request){
    	//dd($request->filter);
    	//$querystringArray = Input::only(['filter']);
        $publishings = Indexing::leftJoin('process_queue', 'indexing.id', '=', 'process_queue.indexing_id')
    						->leftJoin('mst_modes','mst_modes.id', '=', 'process_queue.mode_id')
    						->leftJoin('mst_rfi_type','mst_rfi_type.id', '=', 'process_queue.rfi_type_id')
    						->leftJoin('mst_status','mst_status.id', '=', 'process_queue.status_id')
    						->leftJoin('mst_error_cat','mst_error_cat.id', '=', 'process_queue.error_cat_id')
    						->leftJoin('mst_error_type','mst_error_type.id', '=', 'process_queue.error_type_id')
    						->leftJoin('users','users.id', '=', 'process_queue.rfi_raised_by')
    						->leftJoin('users as err_user','err_user.id', '=', 'process_queue.error_done_by')
    						->leftJoin('users as publisher','publisher.id', '=', 'process_queue.publish_by')
                ->leftJoin('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id')
                ->leftJoin('mst_status as aq_status','aq_status.id', '=', 'audit_queue.audit_status_id')
                ->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id')
                ->leftJoin('mst_request_type','mst_request_type.id', '=', 'indexing.request_type_id')
                ->leftJoin('users as auditor','auditor.id', '=', 'audit_queue.audit_by')

    						->select('process_queue.*','indexing.*','indexing.id as indexing_id', 'mst_rfi_type.rfi_type_name as rfi_name','mst_error_cat.name as err_cat_name', 'mst_error_type.name as err_type_name', 'users.name as rfi_by_name', 'err_user.name as error_done_by_name','publisher.name as publish_by_name','mst_status.status_name as pq_status_name','aq_status.status_name as aq_status_name', 'process_queue.id as process_queue_id','auditor.name as auditor_name','mst_request_type.name as request_type','mst_region.name as region_name')

    						->orderBy('indexing.priority_id','DESC')
    						->orderBy('indexing.mail_received_time');

    						if($request->filter){
					           if($request->filter=='live'){
                                 $publishings->whereIn('mst_status.status_name',['pending out','in process']);
                                 $publishings->orWhereNull('mst_status.status_name');

                                 $publishings->orWhereIn('aq_status.status_name',['pending out','in process']);
                                 //$publishings->orWhereNull('aq_status.status_name');
					           }
					        }
    						
							$publishings = $publishings->get();
                         //dd($result);
        return view('publishing.adminTrackerData',compact('publishings','request'));
    }

    public function partnerData(Request $request)
    {
        $query = PartnerCodeDb::where($request->column,'like', "$request->term%");
           if(isset($request->shipper_name) && !empty($request->shipper_name)){
              $query->where('shipper_name','=', "$request->shipper_name");
           }
           if(isset($request->address) && !empty($request->address)){
             $adr = str_replace('\n',' ',$request->address);
             //echo $request->address;
              $query->where('address','like', "$request->address");
           }
           if(isset($request->city) && !empty($request->city)){
              $query->where('city','=', "$request->city");
           }
           /*if(isset($request->state) && !empty($request->state)){
              $query->where('state','=', "$request->state");
           }*/
           //exit;
           $result = $query->get();

        if($result){
          return json_encode($result);
        }
      
        return 0;

    }

    public function ootEnable(Request $request){
       $publishing = ProcessQueue::find($request->id);
       $request->oot_status = 1;
       $publishing->oot = $request->oot_status;
       $publishing->save();
       $msg='';
       if($request->oot_status==0){
        $msg = "OOT Not Applicable";
       }elseif($request->oot_status==1){
        $msg = "OOT Enabled Successfully";
       }elseif($request->oot_status==2){
        $msg = "OOT Disabled Successfully";
       }
       Session::flash('message',$msg);
            return redirect()->route('publishing.index');
    }

    public function changeStatus($id)
    {
        $publishing = ProcessQueue::findOrFail($id);
        $publishing->delete();
        return redirect()->route('publishing.index'); 
    }
}
