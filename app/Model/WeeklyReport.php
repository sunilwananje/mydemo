<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Region;
use App\Model\Indexing;
use App\Model\AuditingQueue;
use App\Model\ProcessQueue;
use DB,Lava,DateTime;

class WeeklyReport extends Model
{
    
    public static function requestReceived($std,$etd){ //getting count of new request of current day
    	//date_default_timezone_set(TIME_ZONE);
    	//$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	//if($date <= date('Y-m-d')){
	    	$query = Indexing::whereRaw("DATE(indexing.created_at) BETWEEN '$std' AND '$etd'");
	    	
	    	//$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	  
	    	//$query->where('indexing.region_id',$region_id);
	    	
	      //leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	return $query->count();
	    /*}
       return 0;*/
    }
    
    public static function quotedRequest($std,$etd){ //getting sum number of line quotes of current day request
 
        $query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
        $query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
        
        $query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
        $query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
        //$query->where('indexing.region_id',$region_id);
        $query->where(function ($query) use($std,$etd) {
             $query->whereRaw("DATE(audit_queue.audit_end_date) BETWEEN '$std' AND '$etd'");
             $query->orWhereRaw("DATE(process_queue.publish_end_date) BETWEEN '$std' AND '$etd'");
          });
        $query->where(function ($query) {
             $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
             $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
          });
        //$query->where('aq_status.status_name','sent to pricer');
         // $query->groupBy('indexing.region_id');
       
        $result = $query->count();
        if($result)
           return $result;

        return 0;
    
    }

    public static function quoteLineOffered($std,$etd){ //getting sum number of line quotes of current day request
    	//date_default_timezone_set(TIME_ZONE);
    	//$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
      //echo $std.'--'.$etd;
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
        $query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	//$query->where('indexing.region_id',$region_id);
        $query->where(function ($query) use($std,$etd) {
	    	     $query->whereRaw("DATE(audit_queue.audit_end_date) BETWEEN '$std' AND '$etd'");
             $query->orWhereRaw("DATE(process_queue.publish_end_date) BETWEEN '$std' AND '$etd'");
          });
        $query->where(function ($query) {
             $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
             $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
          });
	    	//$query->where('aq_status.status_name','sent to pricer');
	       // $query->groupBy('indexing.region_id');
	     
	    	$result = $query->sum('process_queue.total_lane');
	    	if($result)
	    	   return $result;

	    	return 0;
	  
    }



    public static function inLandQuoteOffered($std,$etd){ //getting sum number of inland quotes of current day request
    	//date_default_timezone_set(TIME_ZONE);
    	//$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));

	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');

	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
        $query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	//$query->where('indexing.region_id',$region_id);
	    	//$query->whereRaw("DATE(audit_queue.audit_end_date) BETWEEN '$std' AND '$etd'");
        $query->where(function ($query) use($std,$etd) {
             $query->whereRaw("DATE(audit_queue.audit_end_date) BETWEEN '$std' AND '$etd'");
             $query->orWhereRaw("DATE(process_queue.publish_end_date) BETWEEN '$std' AND '$etd'");
          });
	    	//$query->where('aq_status.status_name','sent to pricer');
        $query->where(function ($query) {
             $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
             $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
          });
	        //$query->groupBy('indexing.region_id');
	    	$result = $query->sum('process_queue.no_of_inlands');
	    	if($result)
	    	   return $result;

	    	return 0;
	
    }
    public static function sentToCustomer($std,$etd){ //getting count of sent to customer status request
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
            $query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	//$query->where('indexing.region_id',$region_id);
	    	$query->whereRaw("DATE(audit_queue.audit_end_date) BETWEEN '$std' AND '$etd'");
	    	//$query->where('aq_status.status_name','sent to customer');
	        $query->where(function ($query) {
                   $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
                   $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
            });
	        return $query->count();
    }
    public static function tatPer($std,$etd){ // getting count of out of tat request
    	//date_default_timezone_set(TIME_ZONE);
    	//$currentTimestamp = date('Y-m-d H:i:s');
	    	$query = Indexing::leftJoin('process_queue','process_queue.indexing_id','=','indexing.id');
	    	$query->leftJoin('audit_queue','audit_queue.process_queue_id','=','process_queue.id');
	    	//$query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
            $query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
	    	$query->select('aq_status.status_name as aqs','indexing.indexing_tat','indexing.id','indexing.request_no','audit_queue.oot as aq_oot','process_queue.oot as pq_oot','audit_queue.tat_complition');
	    	$query->whereRaw("DATE(audit_queue.audit_end_date) BETWEEN '$std' AND '$etd'");
	    	//$query->whereRaw("TIMESTAMPDIFF(MINUTE,'$currentTimestamp',indexing.indexing_tat)<0");
            //$query->whereIn('aq_status.status_name',['sent to pricer','sent to customer']);
            $query->where(function ($query) {
                   $query->whereIn('aq_status.status_name',['sent to pricer','done','disregard']);
                   $query->orWhereIn('pq_status.status_name',['sent to pricer','done','disregard']);
            });
            $query->where(function ($query) {
                   $query->where('audit_queue.oot','<>',1);
                   $query->orWhere('process_queue.oot','<>',1);
            });
            $query->where(function ($query) {
                   $query->where('audit_queue.tat_complition','<',0);
                  // $query->orWhere('process_queue.tat_complition','<',0);
            });
           // $query->where('audit_queue.oot','<>',1);
            //$query->where('audit_queue.tat_complition','<',0);
	    	/*$query->where(function ($query) {
	               $query->whereNotIn('aq_status.status_name',['pending in','done','sent to customer','sent to pricer','disregard','sent to inside sales']);
	               $query->orWhereNotIn('pq_status.status_name',['pending in','done','disregard','sent to audit']);
	        });

	        $query->where(function ($query) {
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

    public static function errors($std,$etd){ // getting count of cor request for current week
    	//date_default_timezone_set(TIME_ZONE);
       //$date = date('Y-m-d',strtotime($startOfWeek.' +'.$day.' days'));
    	 //$currentTimestamp = date('Y-m-d H:i:s');
    	
	    	$query = Indexing::leftJoin('mst_request_type','mst_request_type.id','=','indexing.request_type_id');
	    	$query->where('mst_request_type.name','cor');
            $query->whereRaw("DATE(indexing.created_at) BETWEEN '$std' AND '$etd'");
	        return $query->count();

    }

    public static function missingTat($std,$etd){
         $stcCount = self::sentToCustomer($std,$etd);
         $tatCount = self::tatPer($std,$etd);
         $missingTat = $stcCount - $tatCount;
         //echo 'mt='.$missingTat.'<br>$stcCoun='.$stcCount.'<br>$tatCount='.$tatCount.'<br>';
         $missingTat = (($stcCount==0)?100:($tatCount/$stcCount*100));
         return $missingTat;
    }

    public function weeklyChart(){
    	date_default_timezone_set(TIME_ZONE);
      $w = $this->getWeekMonFri(-3); //return date of monday and friday of given week offset
      $std1 = $w['Mon']; //date of monday of given week offset
      $etd1 = $w['Fri']; //date of friday of given week offset
      $w1 = $w['W'];     //week number given week offset

      $w = $this->getWeekMonFri(-2);
      $std2 = $w['Mon'];
      $etd2 = $w['Fri'];
      $w2 = $w['W'];

      $w = $this->getWeekMonFri(-1);
      $std3 = $w['Mon'];
      $etd3 = $w['Fri'];
      $w3 = $w['W'];


      $w = $this->getWeekMonFri(0);  
      $std4 = $w['Mon'];
      $etd4 = $w['Fri'];
      $w4 = $w['W'];
    	/*$w = date('W');
    	$y = date('Y');
    	$std1 = date("Y-m-d", strtotime("{$y}-W".($w-3)."-1")); //Returns the date of monday of given week
    	$etd1 = date("Y-m-d", strtotime("{$y}-W".($w-3)."-7")); //Returns the date of sunday of given week

    	$std2 = date("Y-m-d", strtotime("{$y}-W".($w-2)."-1"));
    	$etd2 = date("Y-m-d", strtotime("{$y}-W".($w-2)."-7"));

    	$std3 = date("Y-m-d", strtotime("{$y}-W".($w-1)."-1"));
    	$etd3 = date("Y-m-d", strtotime("{$y}-W".($w-1)."-7"));

    	$std4 = date("Y-m-d", strtotime("{$y}-W{$w}-1"));
    	$etd4 = date("Y-m-d", strtotime("{$y}-W{$w}-7"));*/
        
        $reqRec1 = self::requestReceived($std1,$etd1);  //get number of request for week1
        $reqRec2 = self::requestReceived($std2,$etd2);  //get number of request for week2
        $reqRec3 = self::requestReceived($std3,$etd3);  //get number of request for week3
        $reqRec4 = self::requestReceived($std4,$etd4);  //get number of request for week4

        $quoteLine1 = self::quotedRequest($std1,$etd1); //get requested quote for week1
        $quoteLine2 = self::quotedRequest($std2,$etd2); //get requested quote for week2
        $quoteLine3 = self::quotedRequest($std3,$etd3); //get requested quote for week3
        $quoteLine4 = self::quotedRequest($std4,$etd4); //get requested quote for week4

        $inland1 = self::quoteLineOffered($std1,$etd1);  //get lines quote for week1
        $inland2 = self::quoteLineOffered($std2,$etd2);  //get lines quote for week2
        $inland3 = self::quoteLineOffered($std3,$etd3);  //get lines quote for week3
        $inland4 = self::quoteLineOffered($std4,$etd4);  //get lines quote for week4
        
        $acc1 = self::errors($std1,$etd1);  //get accuracy total for week1
        $acc2 = self::errors($std2,$etd2);  //get accuracy total for week2
        $acc3 = self::errors($std3,$etd3);  //get accuracy total for week3
        $acc4 = self::errors($std4,$etd4);  //get accuracy total for week4

        $tat1 = self::missingTat($std1,$etd1);  //get tat% for week1
        $tat2 = self::missingTat($std2,$etd2);  //get tat% for week2
        $tat3 = self::missingTat($std3,$etd3);  //get tat% for week3
        $tat4 = self::missingTat($std4,$etd4);  //get tat% for week4

        
        /*$sumQuotes = $quoteLine1 + $quoteLine2 + $quoteLine3 + $quoteLine4;
        $sumErrors = $acc1 + $acc2 + $acc3 + $acc4;
      if($sumQuotes > 0)
        $total_acc = 100-(($sumErrors*100)/$sumQuotes);
      else
        $total_acc= 100;*/

        if($quoteLine1 > 0)
         $total_acc1 = 100-(($acc1*100)/$quoteLine1);
        else
        $total_acc1 = 100;

        if($quoteLine2 > 0)
         $total_acc2 = 100-(($acc2*100)/$quoteLine2);
        else
        $total_acc2 = 100;

        if($quoteLine3 > 0)
         $total_acc3 = 100-(($acc3*100)/$quoteLine3);
        else
        $total_acc3 = 100;

        if($quoteLine4 > 0)
         $total_acc4 = 100-(($acc4*100)/$quoteLine4);
        else
        $total_acc4 = 100;
  

    	$chartData = Lava::DataTable(); //creating object for lavachart lib class
		$chartData->addStringColumn('Weeks')
		          ->addNumberColumn('No. of Request Received')
		          ->addNumberColumn('No. of Request Quoted')
		          ->addNumberColumn('No. of Lanes Quoted')
              ->addNumberColumn('TAT(%)')
              ->addNumberColumn('Accuracy(%)')
		          ->addRow(["WK-".($w1), $reqRec1, $quoteLine1, $inland1, $tat1, $total_acc1])
		          ->addRow(["WK-".($w2), $reqRec2, $quoteLine2, $inland2, $tat2, $total_acc2])
		          ->addRow(["WK-".($w3), $reqRec3, $quoteLine3, $inland3, $tat3, $total_acc3])
		          ->addRow(["WK-$w4", $reqRec4, $quoteLine4, $inland4, $tat4, $total_acc4]);
		        // ->addRow(["ACC", $total_acc]);
                
     	$weekChart = Lava::ColumnChart('chartData', $chartData, [
				    'title' => 'Weekly Report',
				    'titleTextStyle' => [
				        'color'    => '#eb6b2c',
				        'fontSize' => 14
				    ],
				    'legend'=> ['position' => 'top'],
				    'series'=> [  
				                      ['targetAxisIndex' => 0],//y1 axis  
                         3 => ['targetAxisIndex' => 1,'type' => 'line','pointShape' => 'triangle','pointSize'=> 10], //y2 axis
				                 4 => ['targetAxisIndex' => 1,'type' => 'line','pointShape' => 'triangle','pointSize'=> 10], //y2 axis
				               ],
				    'vAxes' => [
      						        ['title'=> 'No Of Request'], // y1 axis
      						        ['title'=> '','maxValue'=> 10], // y2 axis
      						     ],
			      'hAxes' => [
			                    ['title'=> 'Weeks'],
			                 ],
				    'seriesType' => 'bars',
				    
				   ]);

     	return  $weekChart;
		
    }

    public function monthlyChart(){   //monthly reportChart
    	date_default_timezone_set(TIME_ZONE);
    	$m = date('m');
      $y = date('y');
      //$year = date('y');

      $std4 = date("Y-m-d", strtotime("{$y}-{$m}-1"));//Returns the first date of given month
      $etd4 = date("Y-m-t", strtotime($std4));//Returns the last date of given month
      
      $m4 = date('m',strtotime($std4));
      $y4 = date('y',strtotime($std4));

      $std3 = date("Y-m-d", strtotime("{$y4}-".($m4-1)."-1"));
      $etd3 = date("Y-m-t", strtotime($std3));

      $m3 = date('m',strtotime($std3));
      $y3 = date('y',strtotime($std3));
      
      $std2 = date("Y-m-d", strtotime("{$y3}-".($m3-1)."-1"));
      $etd2 = date("Y-m-t", strtotime($std2));

      $m2 = date('m',strtotime($std2));
      $y2 = date('y',strtotime($std2));

      $std1 = date("Y-m-d", strtotime("{$y2}-".($m2-1)."-1")); 
      $etd1 = date("Y-m-t", strtotime($std1)); 
      
     // $m2 = date('m',strtotime($std2));
      $y1 = date('y',strtotime($std1));
      //dd($std1,$etd1,$std2,$etd2,$std3,$etd3,$std4,$etd4);

        $ml1 = date('M',strtotime($std1)); //get month in name format for eg. Jan
        $ml2 = date('M',strtotime($std2));
        $ml3 = date('M',strtotime($std3));
        $ml4 = date('M',strtotime($std4));

        $quoteLine1 = self::quotedRequest($std1,$etd1);  //get request quoted for month1
        $quoteLine2 = self::quotedRequest($std2,$etd2);  //get request quoted for month2
        $quoteLine3 = self::quotedRequest($std3,$etd3);  //get request quoted for month3
        $quoteLine4 = self::quotedRequest($std4,$etd4);  //get request quoted for month4

        $inland1 = self::quoteLineOffered($std1,$etd1);   //get lines quoted for month1
        $inland2 = self::quoteLineOffered($std2,$etd2);   //get lines quoted for month2
        $inland3 = self::quoteLineOffered($std3,$etd3);   //get lines quoted for month3
        $inland4 = self::quoteLineOffered($std4,$etd4);   //get lines quoted for month4

        $acc1 = self::errors($std1,$etd1);  //get accuracy total for month1
        $acc2 = self::errors($std2,$etd2);  //get accuracy total for month2
        $acc3 = self::errors($std3,$etd3);  //get accuracy total for month3
        $acc4 = self::errors($std4,$etd4);  //get accuracy total for month4

        $tat1 = self::missingTat($std1,$etd1);  //get tat% for month1
        $tat2 = self::missingTat($std2,$etd2);  //get tat% for month2
        $tat3 = self::missingTat($std3,$etd3);  //get tat% for month3
        $tat4 = self::missingTat($std4,$etd4);  //get tat% for month4

        if($quoteLine1 > 0)
         $total_acc1 = 100-(($acc1*100)/$quoteLine1);
        else
        $total_acc1 = 100;

        if($quoteLine2 > 0)
         $total_acc2 = 100-(($acc2*100)/$quoteLine2);
        else
        $total_acc2 = 100;

        if($quoteLine3 > 0)
         $total_acc3 = 100-(($acc3*100)/$quoteLine3);
        else
        $total_acc3 = 100;

        if($quoteLine4 > 0)
         $total_acc4 = 100-(($acc4*100)/$quoteLine4);
        else
        $total_acc4 = 100;

    	$chartData = Lava::DataTable();  //creating object for lavachart lib class
		  $chartData->addStringColumn('Months')
  		          ->addNumberColumn('No. of Request Quoted')//['role'=>'annotation']
                ->addNumberColumn('No. of Lanes Quoted')
  		          ->addNumberColumn('TAT(%)')
                ->addNumberColumn('Accuracy(%)')
                ->addRow(["$ml1'$y1", $quoteLine1, $inland1, $tat1, $total_acc1])
                ->addRow(["$ml2'$y2", $quoteLine2, $inland2, $tat2, $total_acc2])
                ->addRow(["$ml3'$y3", $quoteLine3, $inland3, $tat3, $total_acc3])
                ->addRow(["$ml4'$y", $quoteLine4, $inland4, $tat4, $total_acc4]);

     	$monthChart = Lava::ColumnChart('chartData', $chartData, [
				    'title' => 'Monthly Report',
				    'titleTextStyle' => [
				        'color'    => '#eb6b2c',
				        'fontSize' => 14
				    ],
				    'legend'=> ['position' => 'top'],
				    'series'=> [  
				                      ['targetAxisIndex' => 0],
                                 2 => ['targetAxisIndex' => 1,'type' => 'line','pointShape' => 'triangle','pointSize'=> 10,],
				                 3 => ['targetAxisIndex' => 1,'type' => 'line','pointShape' => 'triangle','pointSize'=> 10],
				               ],
				    'vAxes'=>[
						      ['title'=> 'No Of Request'], // Left axis
						      ['title'=> '','maxValue'=> 10], // Right axis
						    ],
			        'hAxes' => [
			                    ['title'=> 'Months'],
			                ],
                    'seriesType' => 'bars',

				    /*'annotations' => ['alwaysOutside' => true,
                                       'textStyle' => ['fontSize' => 12,
                                                       'fontName' => 'Times-Roman',
                                                        'auraColor' => '#d799ae',
                                                        'color' => '#555',
                                                      ],
                                       'boxStyle' => [ 'stroke' => '#ccc',
                                                        'strokeWidth' => '1',
                                            'gradient' => [
                                                            'color1'=>'#f3e5f5',
                                                            'color2' => '#f3e5f5',
                                                            'x1' => '0%', 'y1' => '0%',
                                                            'x2' => '100%', 'y2' => '100%'
                                                          ],
                                              
                                            ],
                                            
                                     ],*/
				   ]);

     	return  $monthChart;
		
    }

    public function getWeekMonFri($weekOffset) {
        $dt = new DateTime();
        $dt->setIsoDate($dt->format('o'), $dt->format('W') + $weekOffset);
        return array(
            'Mon' => $dt->format('Y-m-d'),
            'Fri' => $dt->modify('+4 day')->format('Y-m-d'),
            'W' => $dt->format('W'),
        );
    }
  
}
