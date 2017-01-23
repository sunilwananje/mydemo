<?php
namespace App\Helpers;

use Session,DateTime;
//$permissions = Session::get('permissions');
define('SITE_TITLE','F2F SQ Creation');
define('PAGINATE',15);
define('TIME_ZONE', 'America/New_York');
//define('PERMISSION', array());

function timeRemaining($date) // calculate remaining time of TAT
{
    date_default_timezone_set(TIME_ZONE);
    $timestamp = strtotime($date);
    $timestamp = $timestamp - time();
    $min = floor($timestamp/60);
    /*$tat['h']  = floor($timestamp / (60 * 60)); //calculatings hours
    $timestamp -= $tat['h'] * (60 * 60);
    $tat['m']  = round($timestamp / 60); //calculatings minutes
    $timestamp -= $tat['m'] * 60;
    $seconds = floor($timestamp); //calculatings seconds*/
    //echo date('H:i:s',$timestamp);
    return $min;
   
}
function stopTat($pending_in,$tatDate){  //stop tat clock if status is pending in
  date_default_timezone_set(TIME_ZONE);
  $timestamp = strtotime($tatDate)-strtotime($pending_in);
  $min = floor($timestamp/60);
  /*$tat['h']  = floor($timestamp / (60 * 60)); //calculatings hours
  $timestamp -= $tat['h'] * (60 * 60);
  $tat['m']  = round($timestamp / 60); //calculatings minutes
  $timestamp -= $tat['m'] * 60;
  $seconds = floor($timestamp); //calculatings seconds*/

  return $min;
}

function convertToHoursMins($time, $format = '%02d:%02d') { //convert given time to hours:min format
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function getWeek($week, $year) {  //calculate star_date and end_date of given week and year
  $dto = new DateTime();
  $result['start'] = $dto->setISODate($year, $week, 1)->format('Y-m-d'); // date of monday for given week number
  $result['end'] = $dto->setISODate($year, $week, 5)->format('Y-m-d');   // date of friday for given week number
  return $result;
}

?>