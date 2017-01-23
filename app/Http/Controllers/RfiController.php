<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\RFIQueue;
use App\Model\Indexing;
use App\Model\ProcessQueue;
use App\Model\AuditingQueue;

class RfiController extends Controller
{
    public function index()
    {
        
        $rfiData = RFIQueue::join('indexing','indexing.id','=','rfi_queue.indexing_id')
                            ->leftJoin('process_queue','process_queue.id','=','rfi_queue.process_queue_id')
                            ->leftJoin('audit_queue','audit_queue.id','=','rfi_queue.audit_queue_id')
                            ->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id')
                            ->leftJoin('users as auditor','auditor.id','=','audit_queue.audit_rfi_raised_by')
                            ->leftJoin('users as publisher','publisher.id','=','process_queue.rfi_raised_by')
                            ->leftJoin('mst_rfi_type','mst_rfi_type.id', '=', 'process_queue.rfi_type_id')
                            ->leftJoin('mst_status as aq_status','aq_status.id', '=', 'audit_queue.audit_status_id')
                            ->leftJoin('mst_status as pq_status','pq_status.id', '=', 'process_queue.status_id')
                            ->leftJoin('mst_rfi_type as aq_rfi','aq_rfi.id', '=', 'audit_queue.audit_rfi_type_id')
                            ->where('rfi_queue.rfi_status',1)
                            ->select('rfi_queue.*','mst_region.name as region_name','mail_received_time','indexing_tat','request_no','customer_name')
                            ->addSelect('audit_queue.audit_rfi_type_id','audit_queue.audit_rfi_description','audit_queue.audit_rfi_raised_by')
                            ->addSelect('process_queue.rfi_type_id','process_queue.rfi_description','process_queue.rfi_raised_by')
                            ->addSelect('auditor.name as audit_rfi_user','publisher.name as publish_rfi_user')
                            ->addSelect('mst_rfi_type.rfi_type_name as process_rfi_type','aq_rfi.rfi_type_name as audit_rfi_type')
                            ->addSelect('pq_status.status_name as process_status','aq_status.status_name as audit_status')
                            ->get();

                           /* $compData = Indexing::join('process_queue','process_queue.indexing_id','=','indexing.id')
                            ->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id')
                            ->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id')
                            ->leftJoin('users as auditor','auditor.id','=','audit_queue.audit_rfi_raised_by')
                            ->leftJoin('users as publisher','publisher.id','=','process_queue.rfi_raised_by')
                            ->leftJoin('mst_rfi_type','mst_rfi_type.id', '=', 'process_queue.rfi_type_id')
                            ->leftJoin('mst_rfi_type as aq_rfi','aq_rfi.id', '=', 'audit_queue.audit_rfi_type_id')
                            ->where('rfi_queue.rfi_status',1)
                            ->select('rfi_queue.*','mst_region.name as region_name','mail_received_time','indexing_tat','request_no','customer_name')
                            ->addSelect('audit_queue.audit_rfi_type_id','audit_queue.audit_rfi_description','audit_queue.audit_rfi_raised_by')
                            ->addSelect('process_queue.rfi_type_id','process_queue.rfi_description','process_queue.rfi_raised_by')
                            ->addSelect('auditor.name as audit_rfi_user','publisher.name as publish_rfi_user')
                            ->addSelect('mst_rfi_type.rfi_type_name as process_rfi_type','aq_rfi.rfi_type_name as audit_rfi_type');
                          
                            //->get();
                             dd($compData->get(),$compData->toSql());*/
                            //$compData = $this->completedQueue();
                         //return view('rfiQueue.completedView',compact('compData'));
        return view('rfiqueue.rfiView',compact('rfiData'));
    }

    public function completedQueue()
    {
        
        $compData = Indexing::join('process_queue','process_queue.indexing_id','=','indexing.id')
                            ->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id')
                            //->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id')
                            
                            ->leftJoin('users as auditor','auditor.id','=','audit_queue.audit_rfi_raised_by')
                            ->leftJoin('users as publisher','publisher.id','=','process_queue.rfi_raised_by')
                            ->leftJoin('users as aq_user','aq_user.id','=','audit_queue.audit_by')
                            ->leftJoin('users as pq_user','pq_user.id','=','process_queue.publish_by')
                            ->leftJoin('mst_rfi_type','mst_rfi_type.id', '=', 'process_queue.rfi_type_id')

                            ->leftJoin('mst_rfi_type as aq_rfi','aq_rfi.id', '=', 'audit_queue.audit_rfi_type_id')
                            ->leftJoin('mst_status as aq_status','aq_status.id', '=', 'audit_queue.audit_status_id')
                            ->leftJoin('mst_status as pq_status','pq_status.id', '=', 'process_queue.status_id')
                            ->leftJoin('mst_region','mst_region.id', '=', 'indexing.region_id')
                            ->leftJoin('mst_priority_type','mst_priority_type.id', '=', 'indexing.priority_id')
                            //->where('rfi_queue.rfi_status',1)
                            ->select('indexing.*')
                            ->addSelect('audit_queue.audit_rfi_type_id','audit_queue.audit_rfi_description','audit_queue.audit_rfi_raised_by','audit_queue.audit_rfi_end_date','audit_queue.audit_start_date','audit_queue.audit_end_date','audit_queue.oot_remark as aq_oot','audit_queue.comments as aq_comment')
                            ->addSelect('process_queue.rfi_type_id','process_queue.rfi_description','process_queue.rfi_raised_by','process_queue.rfi_end_date','process_queue.publish_start_date','process_queue.publish_end_date','process_queue.oot_remark as pq_oot','process_queue.comments as pq_comment','process_queue.total_lane','process_queue.no_of_inlands')
                            ->addSelect('auditor.name as audit_rfi_user','publisher.name as publish_rfi_user','aq_user.name as audit_user','pq_user.name as publish_user')
                            ->addSelect('mst_rfi_type.rfi_type_name as process_rfi_type','aq_rfi.rfi_type_name as audit_rfi_type','mst_priority_type.name as priority_type','mst_region.name as region_name')
                            
                            ->where(function ($query) {
                                $query->whereRaw('audit_queue.audit_end_date <= now() AND audit_queue.audit_end_date >= now()-interval 3 month');
                                 $query->orWhereRaw('process_queue.publish_end_date <= now() AND process_queue.publish_end_date >= now()-interval 3 month');
                               })// gettin only last 3 months data

                            //->orWhere('pq_status.status_name','done')
                             ->where(function ($query) {
                                 $query->whereIn('aq_status.status_name',['sent to customer','sent to pricer','done','disregard']);
                                 $query->orWhereIn('pq_status.status_name',['disregard','sent to pricer','done']);
                               })
                            
                            ->get();//->toArray();
                             //dd($compData);
                            //return $compData;
        return view('rfiqueue.completedView',compact('compData'));

    }

    
}
