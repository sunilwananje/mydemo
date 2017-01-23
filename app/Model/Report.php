<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Region;
use App\Model\Indexing;
use App\Model\AuditingQueue;
use App\Model\ProcessQueue;
use DB;

class Report extends Model
{
    public static function capa(){ //getting corrective action and prventive action record
	    	$query = Indexing::join('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	$query->leftJoin('mst_error_cat as pq_err','process_queue.error_cat_id','=','pq_err.id');
	    	//$query->leftJoin('mst_error_cat as aq_err','audit_queue.audit_error_cat_id','=','aq_err.id');
	    	
	    	//$query->leftJoin('mst_error_type as pq_err_type','process_queue.error_type_id','=','pq_err_type.id');
	    	//$query->leftJoin('mst_error_type as aq_err_type','audit_queue.audit_error_type_id','=','aq_err_type.id');
	    	$query->leftJoin('mst_status as aq_status','audit_queue.audit_status_id','=','aq_status.id');
	    	$query->leftJoin('mst_status as pq_status','process_queue.status_id','=','pq_status.id');

	    	$query->leftJoin('users as aq_users','audit_queue.audit_error_done_by','=','aq_users.id');
	    	$query->leftJoin('users as pq_users','process_queue.error_done_by','=','pq_users.id');
            
	    	$query->select('error_description','root_cause','correction','corrective_action','preventive_action','proposed_comp_date','proposed_act_date','pq_users.name as pq_user','pq_status.status_name as pq_status_name');
	    	$query->addSelect('audit_error_description','audit_root_cause','audit_correction','audit_corrective_action','audit_preventive_action','audit_proposed_comp_date','audit_proposed_act_date','aq_users.name as aq_user','aq_status.status_name as aq_status_name');
	    	$query->whereNotNull('error_cat_id');
            $query->orWhereNotNull('audit_error_cat_id');

            $result = $query->get();
	    	return $result;

    }

    public static function errors($dates){ //getting cor record
	    	$query = Indexing::join('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	//$query->leftJoin('mst_error_cat as pq_err','process_queue.error_cat_id','=','pq_err.id');
	    	//$query->leftJoin('mst_error_cat as aq_err','audit_queue.audit_error_cat_id','=','aq_err.id');
	    	
	    	$query->leftJoin('mst_error_type as pq_err_type','process_queue.error_type_id','=','pq_err_type.id');
	    	$query->leftJoin('mst_error_type as aq_err_type','audit_queue.audit_error_type_id','=','aq_err_type.id');
	    	//$query->leftJoin('mst_status as aq_status','audit_queue.audit_status_id','=','aq_status.id');
	    	//$query->leftJoin('mst_status as pq_status','process_queue.status_id','=','pq_status.id');
            $query->select('indexing.request_no','indexing.mail_received_time');
	    	$query->leftJoin('users as aq_users','audit_queue.audit_by','=','aq_users.id');
	    	$query->leftJoin('users as pq_users','process_queue.publish_by','=','pq_users.id');
            
	    	$query->addSelect('error_description','root_cause','correction','corrective_action','preventive_action','proposed_comp_date','proposed_act_date','pq_err_type.name as pq_err_type_name','pq_users.name as pq_user');
	    	$query->addSelect('audit_error_description','audit_root_cause','audit_correction','audit_corrective_action','audit_preventive_action','audit_proposed_comp_date','audit_proposed_act_date','aq_err_type.name as aq_err_type_name','aq_users.name as aq_user');

	    	if(!empty($dates)){
            	$std = date('Y-m-d',strtotime($dates['from_date']));
                $etd = date('Y-m-d',strtotime($dates['to_date']));
	    		//$query->whereRaw("DATE(indexing.mail_received_time)  BETWEEN '$std' AND '$etd'");
	    		$query->whereBetween('indexing.mail_received_time',[$std,$etd]);
	    	}
	    	$query->where(function ($query) {
		    	$query->whereNotNull('error_cat_id');
	            $query->orWhereNotNull('audit_error_cat_id');
	        });

            $result = $query->get();
	    	return $result;

    }
     public static function rfiLog($dates){ //getting rfi pending in pending out record

	    	$query = Indexing::join('process_queue','process_queue.indexing_id','=','indexing.id');
	    	//$query = ProcessQueue::join('indexing','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
            $query->leftJoin('mst_status as aq_status','audit_queue.audit_status_id','=','aq_status.id');
	    	$query->leftJoin('mst_status as pq_status','process_queue.status_id','=','pq_status.id');
	    	$query->leftJoin('mst_region','indexing.region_id','=','mst_region.id');
	    	$query->select('indexing.request_no','indexing.mail_received_time','indexing.region_id','customer_name','mst_region.name as region_name');
	    	$query->addSelect('aq_status.status_name as aq_status_name','audit_queue.comments as aq_comment','audit_rfi_description');
	    	$query->addSelect('pq_status.status_name as pq_status_name','process_queue.comments as pq_comment','rfi_description');
            
            if(!empty($dates)){
            	$std = date('Y-m-d',strtotime($dates['from_date']));
                $etd = date('Y-m-d',strtotime($dates['to_date']));
	    		//$query->whereRaw("DATE(indexing.mail_received_time)  BETWEEN '$std' AND '$etd'");
	    		$query->whereBetween('indexing.mail_received_time',[$std,$etd]);
	    	}

            $query->where(function ($query) {
	              $query->whereNotNull('audit_queue.audit_rfi_type_id');
                  $query->orWhereNotNull('process_queue.rfi_type_id');
	        });
	    	$query->where(function ($query) {
	               $query->whereIn('aq_status.status_name',['pending in','pending out','sent to customer','sent to pricer','done']);
	    	       $query->orWhereIn('pq_status.status_name',['pending in','pending out','done']);
	        });
	    	
            $result = $query->get();
	    	return $result;

    }

    
    

}

