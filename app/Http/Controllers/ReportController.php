<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Region;
use App\Model\WeeklyReport;
use App\Model\Report;
use App\Model\User;
use App\Model\UserLogged;
use App\Model\ProcessQueue;
use App\Model\AuditingQueue;
use DB,Input;
use App\Classes\TatCalculator;

class ReportController extends Controller
{
    public function dailyReport(Request $request){
      $regionData = Region::all();
      //$filterString = Input::only(['start_date','end_date']);
      $startDate = $endDate = $dates = '';
      if(!empty($request->from_date)){
        $dates = $request->from_date;
        $date = explode("-",$dates);
        //dd($date);
            if(count($date)>1){
                $startDate = date('Y-m-d',strtotime($date[0]));
                $endDate = date('Y-m-d',strtotime($date[1]));
            }
        
      }
        /*if(!empty($request->to_date)){
        $endDate = date('Y-m-d',strtotime($request->to_date));
      }*/
      $dateArray['from_date'] = $startDate;
      $dateArray['dates'] = $dates;
      //$dateArray['to_date'] = $endDate;
      //dd($dateArray);
        //$endDate = $request->end_date;

      return view('reports.daily',compact('regionData','dateArray'));
    }

    public function weeklyReport(Request $request){
      $startDate = $endDate = $dates = '';
      $report = new WeeklyReport();
      $chartData = $report->weeklyChart();
      $weekArray = array();
        
      if(!empty($request->from_week) && !empty($request->to_week)){
        if($request->from_week > $request->to_week){
          return redirect()->back()->with('error', 'From week must be less than to week!');
        }
           $weekArray['from_week'] = $request->from_week;
           $weekArray['to_week'] = $request->to_week;
           $weekArray['year'] = $request->year;
        }

      return view('reports.weekly',compact('chartData','weekArray'));
    }

    public function monthlyReport(Request $request){
      $startDate = $endDate = $dates = '';
      $report = new WeeklyReport();
      $chartData1 = $report->monthlyChart();
      $monthData = array();
        
      if(!empty($request->from_month) && !empty($request->to_month)){
        if($request->from_month > $request->to_month){
          return redirect()->back()->with('error', 'From month must be less than to month!');
        }
           $monthData['from_month'] = $request->from_month;
           $monthData['to_month'] = $request->to_month;
           $monthData['year'] = $request->year;
        }

      return view('reports.monthly',compact('chartData','monthData'));
    }

    public function capaReport(){
      $report = new Report();
      $capaData = $report->capa();
      return view('reports.capa',compact('capaData'));
    }

    public function errorReport(Request $request){
      $report = new Report();
      $dateArray = array();
      if(!empty($request->from_date) && !empty($request->to_date)){
        if($request->from_date > $request->to_date){
          return redirect()->back()->with('error', 'From date must be less than to date!');
        }
           $dateArray['from_date'] = $request->from_date;
           $dateArray['to_date'] = $request->to_date;
        }

      $errorData = $report->errors($dateArray);
      return view('reports.errors',compact('errorData','dateArray'));
    }

    public function rfiLogReport(Request $request){
      $report = new Report();
      $dateArray = array();
      if(!empty($request->from_date) && !empty($request->to_date)){
        if($request->from_date > $request->to_date){
          return redirect()->back()->with('error', 'From date must be less than to date!');
        }
           $dateArray['from_date'] = $request->from_date;
           $dateArray['to_date'] = $request->to_date;
        }
      $rfiData = $report->rfiLog($dateArray);
      return view('reports.rfilog',compact('rfiData','dateArray'));
    }



    public function getPublishPoint($std,$etd){ // calculate publish points

      $query = AuditingQueue::leftJoin('process_queue','process_queue.id', '=', 'audit_queue.process_queue_id');
      $query->leftJoin('users as audit_user','audit_user.id', '=', 'audit_queue.audit_by');
      $query->leftJoin('users','users.id', '=', 'process_queue.publish_by');
      $query->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id');
      $query->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id');
      $query->select('total_lane','users.name','audit_user.name as auditor');
      $query->whereRaw("process_queue.publish_end_date  BETWEEN '$std' AND '$etd'");
      $query->where(function ($query) {
         $query->whereIn('aq_status.status_name',['sent to pricer','sent to customer','done']);
         $query->orWhereIn('pq_status.status_name',['sent to pricer','sent to customer','done']);
      });
       $publishData = $query->get();
        $publishArray = array();

          foreach($publishData as $publish){
  
            if(!isset($publishArray['publish'][$publish->name])){
              $publishArray['publish'][$publish->name] = 0;
            }
            if(!isset($publishArray['audit'][$publish->auditor])){
              $publishArray['audit'][$publish->auditor] = 0;
            }
  
            if($publish->total_lane >= 1 && $publish->total_lane <= 10){
              $publishArray['publish'][$publish->name] = $publishArray['publish'][$publish->name] + 2;
              $publishArray['audit'][$publish->auditor] = $publishArray['audit'][$publish->auditor] + 1;

            }elseif($publish->total_lane >= 11 && $publish->total_lane <= 20){
              $publishArray['publish'][$publish->name] = $publishArray['publish'][$publish->name] + 4;
              $publishArray['audit'][$publish->auditor] = $publishArray['audit'][$publish->auditor] + 2;

            }elseif($publish->total_lane >= 21 && $publish->total_lane <= 30){
              $publishArray['publish'][$publish->name] = $publishArray['publish'][$publish->name] + 6;
              $publishArray['audit'][$publish->auditor] = $publishArray['audit'][$publish->auditor] + 3;

            }elseif($publish->total_lane >= 31 && $publish->total_lane <= 40){
              $publishArray['publish'][$publish->name] = $publishArray['publish'][$publish->name] + 8;
              $publishArray['audit'][$publish->auditor] = $publishArray['audit'][$publish->auditor] + 4;

            }elseif($publish->total_lane >= 41 && $publish->total_lane <= 50){
              $publishArray['publish'][$publish->name] = $publishArray['publish'][$publish->name] + 10;
              $publishArray['audit'][$publish->auditor] = $publishArray['audit'][$publish->auditor] + 5;

            }elseif($publish->total_lane > 50){
              $publishArray['publish'][$publish->name] = $publishArray['publish'][$publish->name] + 12;
              $publishArray['audit'][$publish->auditor] = $publishArray['audit'][$publish->auditor] + 6;
            }
           
         }
          //dd($publishArray,$publishData->toJSON());
       return $publishArray;
      

    }

    /*public function getAuditPoint($std,$etd){  // calculate audit points

       $auditData = AuditingQueue::join('users','users.id', '=', 'audit_queue.audit_by')
                                ->join('process_queue','process_queue.id','=','audit_queue.process_queue_id')
                                ->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id')
                    ->select('process_queue.total_lane','users.name')
                    ->groupBy('users.id')
                    ->whereRaw("audit_queue.audit_end_date  BETWEEN '$std' AND '$etd'")
                    ->whereIn('aq_status.status_name',['sent to pricer','sent to customer'])
                                  ->get();

         $auditArray = array();

         foreach($auditData as $audit){

             if(!isset($auditArray[$audit->name])){
               $auditArray[$audit->name] = 0;
             }

           if($audit->total_lane >= 1 && $audit->total_lane <= 10){
             $auditArray[$audit->name] = $auditArray[$audit->name] + 1;
           }elseif($audit->total_lane >= 11 && $audit->total_lane <= 20){
             $auditArray[$audit->name] = $auditArray[$audit->name] + 2;
           }elseif($audit->total_lane >= 21 && $audit->total_lane <= 30){
             $auditArray[$audit->name] = $auditArray[$audit->name] + 3;
           }elseif($audit->total_lane >= 31 && $audit->total_lane <= 40){
             $auditArray[$audit->name] = $auditArray[$audit->name] + 4;
           }elseif($audit->total_lane >= 41 && $audit->total_lane <= 50){
             $auditArray[$audit->name] = $auditArray[$audit->name] + 5;
           }elseif($audit->total_lane > 50){
             $auditArray[$audit->name] = $auditArray[$audit->name] + 6;
           }

       }
  
       return $auditArray;
      

    }*/

    public function getPublishQuotes($start_date,$end_date){ //fetch publish quotes for given date range

      $publishQuotes = ProcessQueue::join('users','users.id', '=', 'process_queue.publish_by')
                    //->leftJoin('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id')
                    ->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id')
                    ->select(DB::raw('SUM(total_lane) as publishQuotes'),'users.name')
                    ->whereRaw("process_queue.publish_end_date  BETWEEN '$start_date' AND '$end_date'")
                    ->whereIn('pq_status.status_name',['sent to pricer','sent to customer','sent to audit'])
                    ->groupBy('users.id')
                    ->lists('publishQuotes', 'users.name');
      return $publishQuotes;
    }

    public function getAuditQuotes($start_date,$end_date){ //fetch publish quotes for given date range
      $auditQuotes = ProcessQueue::join('users','users.id', '=', 'process_queue.publish_by')
                    ->join('audit_queue','audit_queue.process_queue_id', '=', 'process_queue.id')
                    ->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id')
                    ->select(DB::raw('SUM(total_lane) as auditQuotes'),'users.name')
                    ->whereRaw("process_queue.publish_end_date  BETWEEN '$start_date' AND '$end_date'")
                    ->whereIn('aq_status.status_name',['sent to pricer','sent to customer'])
                    ->groupBy('users.id')
                    ->lists('auditQuotes', 'users.name');
       return $auditQuotes;
    }

    public function getErrors($start_date,$end_date){ //fetch publish quotes for given date range
      $error = ProcessQueue::leftJoin('audit_queue','process_queue.id', '=', 'audit_queue.process_queue_id')
              ->leftJoin('users as pq_user','pq_user.id', '=', 'process_queue.error_done_by') 
              ->leftJoin('users as aq_user','aq_user.id', '=', 'audit_queue.audit_error_done_by')
              ->leftJoin('mst_status as aq_status','aq_status.id','=','audit_queue.audit_status_id')
              ->leftJoin('mst_status as pq_status','pq_status.id','=','process_queue.status_id')  
              ->where(function ($query) use($start_date,$end_date) {
                     $query->whereNotNull('process_queue.error_done_by')
                         ->where('process_queue.publish_end_date','>=',$start_date)
                           ->where('process_queue.publish_end_date','<=',$end_date);
                           
                  })        
              ->orWhere(function ($query) use($start_date,$end_date) {
                     $query->whereNotNull('audit_queue.audit_error_done_by')
                         ->where('audit_queue.audit_end_date','>=',$start_date)
                         ->where('audit_queue.audit_end_date','<=',$end_date)
                         ->groupBy('aq_user.id');       
                  })
              //->whereIn('aq_status.status_name',['sent to pricer','sent to customer'])
                          
                           ->where(function ($query) {
                         $query->whereIn('aq_status.status_name',['sent to pricer','sent to customer']);
                         $query->orWhereIn('pq_status.status_name',['sent to pricer','done']);
                       })

                 ->groupBy('aq_user.id','pq_user.id')
              //->join('mst_user_access','mst_user_access.id', '=', 'auditing.error_done_by')
              //->join('mst_user_access','mst_user_access.id', '=', 'process_queue.error_done_by')
              
                ->select(DB::raw('count(*) as errorPerUser'),'aq_user.name as aqUserName','pq_user.name as pqUserName')
                
                
                ->lists('errorPerUser', 'aqUserName');
      return $error;
    }

    public function productivityReport(Request $req)
    {
      $finalData = array();
      $datesArray = array();
      
      $days = new TatCalculator();

      if($req->from_date && $req->to_date){
        if($req->from_dat > $req->to_date){
          return redirect()->back()->with('error', 'From date must be less than to date!');
        }
        $start_date = date('Y-m-d 00:00:00', strtotime(date($req->from_date))); 
        $end_date = date('Y-m-d 23:59:59', strtotime(date($req->to_date))); 
        $datesArray['from_date'] = $req->from_date;
        $datesArray['to_date'] = $req->to_date;
      }else{
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-d');//date('Y-m-t 23:59:59'); 
      }
  
      $totalWorkingDays = $days->countWorkingDays($start_date,$end_date);

      $points = $this->getPublishPoint($start_date,$end_date);

      $publish = ((isset($points['publish']))?$points['publish']:''); // find publish points

      $audit = ((isset($points['audit']))?$points['audit']:'');//$this->getAuditPoint($start_date,$end_date); // find audit points

      $publishQuotes = $this->getPublishQuotes($start_date,$end_date); // find publish quotes

      $auditQuotes = $this->getAuditQuotes($start_date,$end_date); // find audit quotes

      $error = $this->getErrors($start_date,$end_date); // find errors form publish and audit queue
        
      $workingDays = UserLogged::join('users','users.id', '=', 'user_logged_details.user_id')
                    ->select(DB::raw('count(*) as workingDays'),'users.name') 
                    ->whereRaw("user_logged_details.login_time BETWEEN '$start_date' AND '$end_date'")
                    ->groupBy('users.id')               
                    ->lists('workingDays', 'users.name'); //query to get working days of each users

       $userData = User::all(); //query to get all user

       $finalData = $this->mergeData($publish, $audit, $error, $workingDays, $publishQuotes, $auditQuotes, $userData); //combine all productivity data in single array         
      
       $finalData['totalWorkingDays'] = $totalWorkingDays;

      return View('reports.productivity',compact('userData','finalData','datesArray'));
      //return View('reports.productivity',compact('userData','finalData','publish','audit','publishQuotes','auditQuotes','error','workingDays','start_date','end_date'));
    }

    public function mergeData($publish,$audit,$error,$workingDays,$publishQuotes,$auditQuotes,$userData){  //merge all productivity data in single array
       
       $finalData['sum'] = 0; $userCount = 0;
        foreach($userData as $user){

            $finalData['publish'][$user->name]    = isset($publish[$user->name]) ? $publish[$user->name] : '0';
            $finalData['audit'][$user->name]      = isset($audit[$user->name]) ? $audit[$user->name] : '0';
            $finalData['total'][$user->name]      = $finalData['publish'][$user->name] + $finalData['audit'][$user->name];

            $finalData['sum'] = $finalData['sum'] + $finalData['total'][$user->name];

            $finalData['error'][$user->name]    = isset($error[$user->name]) ? $error[$user->name] : '0';
            $finalData['workingDays'][$user->name] = isset($workingDays[$user->name]) ? $workingDays[$user->name] : '0';
            $finalData['publishQuotes'][$user->name] = isset($publishQuotes[$user->name]) ? $publishQuotes[$user->name] : '0';
            $finalData['auditQuotes'][$user->name] = isset($auditQuotes[$user->name]) ? $auditQuotes[$user->name] : '0';
                

            //$finalData['publishPro'][$user->name] = $finalData['publish'][$user->name]*100/(19*20);
            //$finalData['auditPro'][$user->name]   = $finalData['audit'][$user->name]*100/(19*20);
            //$finalData['actualPro'][$user->name]  = $finalData['publishPro'][$user->name] + $finalData['auditPro'][$user->name];
            $finalData['accuracy'][$user->name]   = ($finalData['total'][$user->name] != 0) ? 100-($finalData['error'][$user->name]*100)/$finalData['total'][$user->name] : 0;

            if($finalData['audit'][$user->name]!=0 || $finalData['publish'][$user->name]!=0){
              $userCount = $userCount+1;
            }
        }
        if($userCount!=0){
          $finalData['mqc'] = $finalData['sum'] / $userCount;
        }else{
          $finalData['mqc'] = 0;
        }
      return $finalData;
    }
}
