@extends('layouts.default')
   @section('title')
    Daily Report
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <!-- <link rel="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui.css') }}">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.css') }}">
     <style>
       .text-center{
         text-align:center !important;
         vertical-align:middle !important;
       }
       .cell-bg{
         background: #3c8dbc;color: white !important;
       }
     </style>
  @endsection
<?php 
// date_default_timezone_set(TIME_ZONE);
 $week = date('W');
 $year = date('Y');
 //print_r($dateArray);
 if(empty($dateArray['from_date'])){
   $startOfWeek = (date('l') == 'Monday') ? date('Y-m-d') : date('Y-m-d', strtotime("last monday 00:00"));
   $sowTimestamp = (date('l') == 'Monday') ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime("last monday 00:00"));
  }else{
    $startOfWeek = $dateArray['from_date'];
    $sowTimestamp = date('Y-m-d H:i:s', strtotime($startOfWeek));
  }

 $currentDate = date('d/M/Y');
 $regionCount = count($regionData);
 $dayArray = array('MON','TUE','WED','THUR','FRI');
 $dayCount = count($dayArray);
 $totalColspan = $regionCount * $dayCount;
 $total = 0;
?>
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Daily Report</h3>
                  <!-- <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','daily_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a> -->    
                   <div class="row">
                       <form class="form-horizontal" autocomplete="off">
                       <div class='col-md-1'></div>
                       <div class='col-md-4'>
                         <label for="from_date" class="control-label">Select Week Dates </label> 
                          <div class="input-group date">
                          <input type='text' class="form-control week-picker" name="from_date" id="from_date" value="{{$dateArray['dates']}}"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          </div>
                        </div>
                       <!--  <div class='col-md-4'>
                         <label for="to_date" class="control-label">To Date </label> 
                          <div class="input-group date">
                          <input type='text' class="form-control datetimepicker" name="to_date" id="to_date" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          </div>
                        </div> -->
                        <div class="col-md-7" style="margin-top: 27px;">
                          <button type="submit" class="btn btn-primary">
                              <i class="fa fa-btn fa-search"></i> Search
                          </button>
                             <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','daily_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a>
                        </div>
                        
                        </form>
                   </div> 
                     <div class="clearfix"></div>
                </div>

                <div class="box-body">
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered" style="font-size:small">
                          <!-- <thead style="background: #3c8dbc;color: white;">
                            <tr>
                              <td></td>
                            </tr>
                         </thead> -->
                         <tbody>
                            <tr class="cell-bg">
                              <th width="15%">Week Number/Year</th>
                              <th width="25%">{{ $week.'/'.$year }}</th>
                              <th rowspan="3" colspan="{{ $totalColspan }}" class="text-center">CMA-CGM</th>
                            </tr>
                            <tr class="cell-bg">
                              <th>Agency</th>
                              <th>NORFOLK USA</th>  
                            </tr>
                            <tr class="cell-bg">
                              <th>Week start date</th>
                              <th>{{ date('d/M/Y', strtotime($startOfWeek)) }}</th> 
                            </tr>
                            <tr class="cell-bg">
                              <th>1</th>
                              <th>Status of Process</th>
                              <th colspan="{{ $totalColspan }}" class="text-center">F2F SQ Creation</th>
                            </tr>
                            <tr>
                              <td rowspan="9"></td>
                              <th>Regions</th>
                              @foreach($regionData as $region)
                              <th colspan="{{ $dayCount }}" class="text-center">{{ $region->name }}</th>
                              @endforeach
                            </tr>
                            <tr>
                              <th>Average volume per week prior to SSC</th>
                              @foreach($regionData as $region)
                              <th colspan="{{ $dayCount }}" class="text-center">{{ $region->region_volume }}</th>
                              @endforeach
                            </tr>
                            <tr>
                              <th>Activities</th>
                               @foreach($regionData as $region)
                                @foreach($dayArray as $day)
                                <th>{{ $day }}</th>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>Opening Balance (No of Request)</td>
                              @foreach($regionData as $i => $region)
                               
                                @foreach($dayArray as $k => $day)
                                <td>
                                 <?php 
                                  $ner[$i][$k] = App\Model\DailyReport::newRequest($region->id,$startOfWeek,$k);
                                  $stc[$i][$k] = App\Model\DailyReport::sentToCustomer($region->id,$startOfWeek,$k);
                                  $date = date('Y-m-d',strtotime($startOfWeek.' +'.$k.' days'));
                                 ?>
                                 @if($day == 'MON')
                                  {{ $opb[$i][$k] = App\Model\DailyReport::requestOpeningBalance($region->id,$startOfWeek,$k) }}
                                 @else
                                   @if($date <= date('Y-m-d'))
                                   <?php $opb[$i][$k] =  $opb[$i][$k-1] + $ner[$i][$k-1] - $stc[$i][$k-1]; ?>
                                   {{ (($opb[$i][$k]>0)?$opb[$i][$k]:0) }}
                                   @else
                                    {{ $opb[$i][$k] =  0 }}
                                   @endif
                                 @endif
                                </td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of New Request received(email)</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <td>
                                   {{ $ner[$i][$k] }}
                                 
                                </td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of quotes offered(quote lines)</td>
                              @foreach($regionData as $region)
                                @foreach($dayArray as $k => $day)
                                <td>{{ App\Model\DailyReport::quoteLineOffered($region->id,$startOfWeek,$k) }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of inland Quote Offered</td>
                              @foreach($regionData as $region)
                                @foreach($dayArray as $k => $day)
                                <td>{{ App\Model\DailyReport::inLandQuoteOffered($region->id,$startOfWeek,$k) }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of Request sent to Customer</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <td>{{ $stc[$i][$k] }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>Closing Balance (No of Request)</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                 <?php 
                                   $total = $opb[$i][$k] + $ner[$i][$k] - $stc[$i][$k];
                                 ?>
                                <td>{{ (($total<0)?0:$total) }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr class="cell-bg">
                              <th>2</th>
                              <th>RFI</th>
                              <th colspan="{{ $totalColspan }}"></th>
                              
                            </tr>
                            <tr>
                              <td rowspan="4"></td>
                              <td>Opening Balance (No of RFI)</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <td>
                                   <!-- {{ $orfi[$i][$k] = App\Model\DailyReport::openRFI($region->id,$startOfWeek,$k) }} -->
                                   <?php 
                                    $nrfi[$i][$k] = App\Model\DailyReport::newRFI($region->id,$startOfWeek,$k);
                                    $crfi[$i][$k] = App\Model\DailyReport::solvedRFI($region->id,$startOfWeek,$k);
                                    $date = date('Y-m-d',strtotime($startOfWeek.' +'.$k.' days'));
                                 ?>
                                 @if($day == 'MON')
                                  {{ $orfi[$i][$k] = App\Model\DailyReport::openRFI($region->id,$startOfWeek,$k) }}
                                 @else
                                   @if($date <= date('Y-m-d'))
                                   <?php $orfi[$i][$k] =  $orfi[$i][$k-1] + $nrfi[$i][$k-1] - $crfi[$i][$k-1]; ?>
                                   {{ (($orfi[$i][$k]>0)?$orfi[$i][$k]:0) }}
                                   @else
                                    {{ $orfi[$i][$k] =  0 }}
                                   @endif
                                 @endif
                                </td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of New RFI raised </td>
                              @foreach($regionData as $i =>$region)
                                @foreach($dayArray as $k => $day)
                                <td>{{ $nrfi[$i][$k] }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of RFI resolved</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <td>{{ $crfi[$i][$k] }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>No. of RFI pending resolution</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <?php 
                                 $total = $orfi[$i][$k] + $nrfi[$i][$k] - $crfi[$i][$k];
                                 ?>
                                <td>{{ (($total<0)?0:$total) }} </td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr class="cell-bg">
                              <th>3</th>
                              <th>T.A.T Conformity</th>
                              <th colspan="{{ $totalColspan }}"></th>
                            </tr>
                            <tr>
                              <td rowspan="2"></td>
                              <td>No. of Request out of TAT</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <td>{{ $tat[$i][$k] = App\Model\DailyReport::outofTAT($region->id,$startOfWeek,$k) }}</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr>
                              <td>TAT Missed%</td>
                              @foreach($regionData as $i => $region)
                                @foreach($dayArray as $k => $day)
                                <?php $t = (($stc[$i][$k]==0)?0:($tat[$i][$k]/$stc[$i][$k]*100)); ?>
                                <td>{{ round($t,2) }}%</td>
                                @endforeach
                               @endforeach
                            </tr>
                            <tr class="cell-bg">
                              <th>4</th>
                              <th>Accuracy - YTD</th>
                              <td colspan="{{ $totalColspan }}"></td>
                              
                            </tr>
                            <tr>
                              <td></td>
                              <td>Errors Received from Agency (All)</td>
                              <th colspan="{{ $totalColspan }}" class="text-center">{{ App\Model\DailyReport::accuracyYTD($region->id,$startOfWeek) }}</th>
                            </tr>
                            
                         </tbody>
                      </table>
                   </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
  <!-- modal end -->
@endsection

@section('script')
<script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
<!-- <script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.js') }}"></script> -->
<script src="{{ asset('/assets/dist/js/jquery.table2excel.js') }}"></script>
<script type="text/javascript">
$(function() {
    var startDate;
    var endDate;
    
    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }
    
    $('.week-picker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        beforeShowDay: $.datepicker.noWeekends,
        maxDate:0,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            //alert(date);
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay()+1);
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
            $(this).val($.datepicker.formatDate( dateFormat, startDate, inst.settings)+'-'+$.datepicker.formatDate( dateFormat, endDate, inst.settings ));
            
            selectCurrentWeek();
        },
        /*beforeShowDay: function(date) {
            var cssClass = '';
            if(date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';
            return [true, cssClass];
        },*/
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentWeek();
        }
    });
    
    $(document).on('mouseover','.ui-datepicker-calendar tr', function() { 
      //console.log("success");
      $(this).find('td a').addClass('ui-state-hover'); 
    });
    $(document).on('mouseleave','.ui-datepicker-calendar tr', function() { 
      $(this).find('td a').removeClass('ui-state-hover');
    });
   // $('.ui-datepicker-calendar tr').mouseleave( function() { $(this).find('td a').removeClass('ui-state-hover'); });
});

       
      var exportThisWithParameter = (function () {
          var uri = 'data:application/vnd.ms-excel;base64,', template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"  xmlns="http://www.w3.org/TR/REC-html40"><head> <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets> <x:ExcelWorksheet><x:Name>{worksheet}</x:Name> <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions> </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook> </xml><![endif]--></head><body> <table>{table}</table></body></html>',
          base64 = function (s) {
                 return window.btoa(unescape(encodeURIComponent(s)))
          },
          format = function (s, c) {
                 return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
          }
          return function (tableID, excelName) {
                 tableID = document.getElementById(tableID)
                 var ctx = { worksheet: excelName || 'Worksheet', table: tableID.innerHTML }
                 var link = document.createElement("A");
                 link.href = uri + base64(format(template, ctx));
                 link.download = 'daily_report_{{time()}}.xls';
                 link.target = '_blank';
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
          }
   })();

   
</script>

@endsection

           