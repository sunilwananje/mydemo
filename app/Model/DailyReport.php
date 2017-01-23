<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Region;
use App\Model\Indexing;
use App\Model\AuditingQueue;
use App\Model\ProcessQueue;
use App\Model\RFIQueue;
use DB;

class DailyReport extends Model
{
    public static function requestOpeningBalance($region_id,$startOfWeek,$day){ //getting count of pendding request of past days

    	//date_default_timezone_set(TIME_ZONE);
      
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	$d = date('D',strtotime($date));

    	$previous_week = strtotime("-1 week +1 day");
		$start_week = strtotime("last sunday midnight +1 day",$previous_week);
		$end_week = strtotime("next saturday -1 day",$start_week);
		$start_week = date("Y-m-d",$start_week);
		$end_week = date("Y-m-d",$end_week);

		//echo $start_week.'--'.$end_week;
 
    	//echo $d;
        /*if($d=='Mon'){
           $date = date('Y-m-d',strtotime($date.' -3 days')); //get date of friday
        }else{
           $date = date('Y-m-d',strtotime($date.' -1 days'));
        }*/
    	//$date = date('Y-m-d',strtotime('2016-10-31'.' -3 days'));
    	//echo $date.'  ';

    	if($date<=date('Y-m-d')){
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(indexing.created_at) < '$date'");
	    	//$query->whereRaw("DATE(indexing.created_at) >= '$start_week'");
	    	$query->whereRaw("DATE(indexing.created_at) <= '$end_week'");
	    	$query->where(function ($query) {
	               $query->whereNotIn('aq_status.status_name',['done','disregard','sent to customer','sent to inside sales','sent to pricer']);
	               $query->orwhere(function ($query) {
	                  $query->whereNotIn('pq_status.status_name',['sent to pricer','done','disregard','sent to audit']);
	                  $query->orWhereNull('process_queue.status_id');
	               });
	        });
	    	
	    	return $query->count();
	    }
       return 0;
    	
    }
    public static function newRequest($region_id,$startOfWeek,$day){ //getting count of new request of current day
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	if($date <= date('Y-m-d')){
	    	$query = Indexing::where('indexing.region_id',$region_id);
	    	//leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	
	    	//$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	  
	    	//$query->where('indexing.region_id',$region_id);
	    	$query->whereRaw("DATE(indexing.created_at) = '$date'");
	    
	    	return $query->count();
	    }
       return 0;
    }
    public static function quoteLineOffered($region_id,$startOfWeek,$day){ //getting sum number of line quotes of current day request
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	if($date<=date('Y-m-d')){
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');

	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	$query->where(function ($query) use($date) {
	              $query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	      $query->orWhereRaw("DATE(process_queue.publish_end_date) = '$date'");
	        });
	    	//$query->where('aq_status.status_name','sent to pricer');
	    	$query->where(function ($query) {
	               $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
	    	      $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
	        });
	    
	        $query->groupBy('indexing.region_id');
	 
	    	$result = $query->sum('process_queue.total_lane');
	    	if($result)
	    	   return $result;

	    	return 0;
	    }
       return 0;
    }
    //8354400218

    public static function inLandQuoteOffered($region_id,$startOfWeek,$day){ //getting sum number of inland quotes of current day request
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	if($date<=date('Y-m-d')){
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');

	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	//$query->where('aq_status.status_name','sent to pricer');
            $query->where(function ($query) use($date) {
	              $query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	      $query->orWhereRaw("DATE(process_queue.publish_end_date) = '$date'");
	        });
	    	$query->where(function ($query) {
	               $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
	    	      $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
	        });
	        $query->groupBy('indexing.region_id');
	    	$result = $query->sum('process_queue.no_of_inlands');
	    	if($result)
	    	   return $result;

	    	return 0;
	    }
       return 0;
    }

    public static function sentToCustomer($region_id,$startOfWeek,$day){ //getting count of sent to customer status request
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	if($date<=date('Y-m-d')){
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');

	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(indexing.created_at) = '$date'");
	    	//$query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	//$query->where('aq_status.status_name','sent to customer');
	    	$query->where(function ($query) use($date) {
	              $query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	      $query->orWhereRaw("DATE(process_queue.publish_end_date) = '$date'");
	        });
	    	$query->where(function ($query) {
	               $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
	    	      $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
	        });
	  
	        return $query->count();
	    }
       return 0;
    }

   public static function openRFI($region_id,$startOfWeek,$day){ //getting open rfi pending in count of current day
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	/*$d = date('D',strtotime($startOfWeek.' +'.$day.' days'));
    	if($d=='Mon'){
           $date = date('Y-m-d',strtotime($date.' -3 days')); //get date of friday
        }else{
           $date = date('Y-m-d',strtotime($date.' -1 days'));
        }*/

        $previous_week = strtotime("-1 week +1 day");
		$start_week = strtotime("last sunday midnight +1 day",$previous_week);
		$end_week = strtotime("next saturday -1 day",$start_week);
		$start_week = date("Y-m-d",$start_week);
		$end_week = date("Y-m-d",$end_week);

    	if($date<=date('Y-m-d')){
	    	/*$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');*/
	    	$query = RFIQueue::join('indexing','indexing.id','=','rfi_queue.indexing_id');
            $query->leftJoin('process_queue','process_queue.id','=','rfi_queue.process_queue_id');
            $query->leftJoin('audit_queue','audit_queue.id','=','rfi_queue.audit_queue_id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(process_queue.rfi_start_date) = '$date'");
	    	/*$query->where(function ($query) {
	               $query->where('aq_status.status_name','pending in');
	               $query->orWhere('pq_status.status_name','pending in');
	        });*/
	        //$query->whereRaw("DATE(indexing.created_at) >= '$start_week'");
	    	//$query->whereRaw("DATE(indexing.created_at) <= '$end_week'");
	        /*$query->where(function ($query) use ($date) {
	               $query->whereRaw("DATE(audit_queue.audit_rfi_start_date) > '0000-00-00' AND DATE(audit_queue.audit_rfi_start_date) = '$date'");
	               $query->orWhereRaw("DATE(process_queue.rfi_start_date) > '0000-00-00' AND DATE(process_queue.rfi_start_date) = '$date'");
	        });*/
	        /*$query->where(function ($query) use ($start_week,$end_week) {
	               $query->whereRaw("DATE(audit_queue.audit_rfi_start_date) > '0000-00-00' AND DATE(audit_queue.audit_rfi_start_date) >= '$start_week' AND DATE(audit_queue.audit_rfi_start_date) <= '$end_week'");
	               $query->orWhereRaw("DATE(process_queue.rfi_start_date) > '0000-00-00' AND DATE(process_queue.rfi_start_date) >= '$start_week' AND DATE(process_queue.rfi_start_date) <= '$end_week'");
	        });
	        $query->where(function ($query) {
	               $query->whereRaw("DATE(audit_queue.audit_rfi_end_date) = '0000-00-00'");
	               $query->orWhereRaw("DATE(process_queue.rfi_end_date) = '0000-00-00'");
	        });*/
	         //$query->whereRaw("DATE(rfi_queue.rfi_start_date) > '0000-00-00' AND DATE(rfi_queue.rfi_start_date) >= '$start_week' AND DATE(rfi_queue.rfi_start_date) <= '$end_week'");
	        $query->whereRaw("DATE(rfi_queue.rfi_start_date) > '0000-00-00' AND DATE(rfi_queue.rfi_start_date) <= '$end_week'");
	        $query->whereRaw("DATE(rfi_queue.rfi_end_date) = '0000-00-00'");
	        return $query->count();
	    
	    }
       return 0;
    }

    public static function newRFI($region_id,$startOfWeek,$day){ //getting new rfi pending in count of current day
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	if($date<=date('Y-m-d')){
	    	/*$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');*/
	    	$query = RFIQueue::join('indexing','indexing.id','=','rfi_queue.indexing_id');
            $query->leftJoin('process_queue','process_queue.id','=','rfi_queue.process_queue_id');
            $query->leftJoin('audit_queue','audit_queue.id','=','rfi_queue.audit_queue_id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(process_queue.rfi_start_date) = '$date'");
	    	/*$query->where(function ($query) {
	               $query->where('aq_status.status_name','pending in');
	               $query->orWhere('pq_status.status_name','pending in');
	        });*/
	        $query->whereRaw("DATE(rfi_queue.rfi_start_date) = '$date'");
	        //$query->whereRaw("audit_queue.audit_rfi_end_date = '0000-00-00 00:00:00'");
	        /*$query->where(function ($query) use ($date) {
	               $query->whereRaw("DATE(audit_queue.audit_rfi_start_date) = '$date'");
	               $query->orWhereRaw("DATE(process_queue.rfi_start_date) = '$date'");
	        });
	        $query->where(function ($query) {
	               $query->whereRaw("audit_queue.audit_rfi_end_date = '0000-00-00 00:00:00'");
	               $query->orWhereRaw("process_queue.rfi_end_date = '0000-00-00 00:00:00'");
	        });*/
	        return $query->count();
	    }
       return 0;
    }

    public static function solvedRFI($region_id,$startOfWeek,$day){ // getting rfi resolved/pending out count of current week
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	if($date<=date('Y-m-d')){
	    	/*$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');*/
	    	$query = RFIQueue::join('indexing','indexing.id','=','rfi_queue.indexing_id');
            $query->leftJoin('process_queue','process_queue.id','=','rfi_queue.process_queue_id');
            $query->leftJoin('audit_queue','audit_queue.id','=','rfi_queue.audit_queue_id');
	    	$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(process_queue.rfi_start_date) = '$date'");
	    	/*$query->where(function ($query) {
	               $query->where('aq_status.status_name','pending out');
	               $query->orWhere('pq_status.status_name','pending out');
	        });*/
	        $query->whereRaw("DATE(rfi_queue.rfi_end_date) = '$date'");
	        /*$query->where(function ($query) use ($date) {
	               $query->whereRaw("DATE(audit_queue.audit_rfi_end_date) = '$date'");
	               $query->orWhereRaw("DATE(process_queue.rfi_end_date) = '$date'");
	        });*/
	        return $query->count();
	    }
       return 0;
    }

    public static function outofTAT($region_id,$startOfWeek,$day){ // getting count of out of tat request
    	//date_default_timezone_set(TIME_ZONE);
    	$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	$currentTimestamp = date('Y-m-d H:i:s');
    	if($date <= date('Y-m-d')){
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	//$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
	    	$query->where('indexing.region_id',$region_id);
	    	$query->select('aq_status.status_name as aqs','indexing.indexing_tat','indexing.id','indexing.request_no','audit_queue.oot as aq_oot','process_queue.oot as pq_oot');
	    	//$query->whereRaw("DATE(indexing.indexing_tat) = '$date'");
	    	$query->whereRaw("DATE(audit_queue.audit_end_date) = '$date'");
	    	$query->whereIn('aq_status.status_name',['sent to pricer','sent to customer']);
            $query->where('audit_queue.oot','<>',1);
            $query->where('audit_queue.tat_complition','<',0);
	    	/*if($date==date('Y-m-d')){
	    	    $query->whereRaw("TIMESTAMPDIFF(MINUTE,'$currentTimestamp',indexing.indexing_tat)<0");
	    	}else{
	    		$query->whereRaw("TIMESTAMPDIFF(MINUTE,'$date',indexing.indexing_tat)<0");
	    	}*/

            
	    	/*$query->where(function ($query) {
	               $query->whereNotIn('aq_status.status_name',['pending in','done','sent to customer','sent to pricer','disregard','sent to inside sales']);
	               //$query->orWhereNull('audit_queue.audit_status_id');
	               $query->orWhereNotIn('pq_status.status_name',['pending in','done','disregard','sent to audit']);
	               //$query->orWhereNull('process_queue.status_id');
	        });*/

	        /*$query->where(function ($query) {
	        	 $query->where(function ($query) {
	               $query->where('audit_queue.oot',0);
	               $query->orWhereNull('audit_queue.oot');
	             } );
	             $query->where(function ($query) {
	               $query->where('process_queue.oot',0);
	               $query->orWhereNull('process_queue.oot');
	             } );
	               
	        });*/
	        $result = $query->get();
	        return count($result);
	    }
       return 0;
    }

    public static function accuracyYTD($region_id,$startOfWeek){ // getting count of cor request for current week
    	//date_default_timezone_set(TIME_ZONE);
       //$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	 $currentTimestamp = date('Y-m-d H:i:s');
    	
	    	$query = Indexing::leftJoin('mst_request_type','mst_request_type.id','=','indexing.request_type_id');
	    	$query->where('mst_request_type.name','cor');

	        return $query->count();

    }

}
