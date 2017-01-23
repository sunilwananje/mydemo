<?php
namespace App\Classes;

use App\Model\Holiday;
use App\Helpers;

class TatCalculator 
{
    public function calculateTat($hr,$request_date){
      date_default_timezone_set(TIME_ZONE);
      $deadline = date('Y-m-d H:i:s',strtotime($request_date.' +'.$hr.' hours'));
      $day = date('D',strtotime($deadline));
      if($day=='Sat'){
         $deadline = date('Y-m-d H:i:s',strtotime($deadline.' +2 Days'));
      }elseif($day=='Sun'){
          $deadline = date('Y-m-d H:i:s',strtotime($deadline.' +1 Days'));       
      }
      $final_deadline = $this->calculateHoliday($deadline);
      //echo $hr.' '.$request_date;
      return $final_deadline;
    }

    public function calculateHoliday($deadline){
      date_default_timezone_set(TIME_ZONE);
      $holidaysObj = Holiday::select('holiday_date')->get()->toArray();
      $holidayArray = array_column($holidaysObj, 'holiday_date');//converting multidim array to single dim array
      $date = date('Y-m-d',strtotime($deadline));
      if(in_array($date,$holidayArray)){
         $deadline = date('Y-m-d H:i:s',strtotime($deadline.' +1 Days')); //add 1 day to last date
         $this->calculateHoliday($deadline); //check whether deadline has holiday or not recurrsively
      }
      return $deadline;
    }

    public function rfiDeadline($pending_in,$pending_out,$tat){
      date_default_timezone_set(TIME_ZONE);
      if($pending_in!='0000-00-00 00:00:00'){
        $pending_differnce_time = strtotime($pending_out)-strtotime($pending_in);
        $deadline = date('Y-m-d H:i:s',strtotime($tat.' +'.$pending_differnce_time.' seconds'));
        //dd(strtotime($pending_out),strtotime($pending_in),$pending_differnce_time,$pending_in,$pending_out,$tat)
      }else{
        $deadline = $tat;
      }
    
      return $deadline;
    }

    public function countWorkingDays($std, $etd) //calculate no of working days between two dates 
    {
        $start = strtotime($std);
        $end = strtotime($etd);
        $iter = 24*60*60; // whole day in seconds
        $count = 0; // keep a count of Sats & Suns
        $holidaysObj = Holiday::select('holiday_date')->whereBetween('holiday_date',[$std, $etd])->get()->toArray();
        $holidayArray = array_column($holidaysObj, 'holiday_date');//converting multidim array to single dim array
        $datediff = $end - $start; 
        $totalDays =  round($datediff / $iter); //get number of days

        for($i = $start; $i <= $end; $i = $i+$iter){
            if(Date('D',$i) == 'Sat') {
                $count++;
            }else if(Date('D',$i) == 'Sun'){
              $count++;
            }else if(in_array(date('Y-m-d',$i),$holidayArray)){
              $count++;
            }
        }
         //dd($count, $totalDays, ($totalDays - $count),$holidayArray);
        return ($totalDays - $count);
   }

}
