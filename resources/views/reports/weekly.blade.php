@extends('layouts.default')
   @section('title')
    Weekly Report
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <!-- <link rel="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui.css') }}">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.css') }}">
  @endsection

  <?php 

     if(empty($weekArray['from_week']) || empty($weekArray['to_week']) || empty($weekArray['year'])){
        $weekArray['from_week']  = 1;
        $weekArray['to_week']    = date('W'); 
        $weekArray['year']       = date('Y'); 
        
     }
      $i              = $weekArray['from_week'];
      $currentWeek    = $weekArray['to_week'];

      $year           = $weekArray['year'];
      $dt             = strtotime("31 December $year");
      $cWeek          = gmdate("W", $dt);
      $cYear          = date('Y');
      /*$week           = (($i == 1)?0:( $i * 7 * 24 * 60 * 60 ));
      $firstDayOfYear = mktime( 0, 0, 0, 1, 1,  $year ) + $week;//mktime(0, 0, 0, 1, 1, $year);
      $nextMonday     = strtotime('monday', $firstDayOfYear);
      $nextFriday     = strtotime('friday', $nextMonday);
      $startDate      = date('Y-m-d', $nextMonday);
      $endDate        = date('Y-m-d', $nextFriday);
      //dd($startDate,$endDate);
      while (date('Y', $nextMonday) == $year) {
        echo $i." --> ";
          echo date('d-m-Y', $nextMonday).' - '.date('d-m-Y', $nextFriday).'<br>';

          $nextMonday = strtotime('+1 week', $nextMonday);
          $nextFriday = strtotime('+1 week', $nextFriday);
          print_r(App\Helpers\getWeek(47,'2016'));
          echo "<br> ";
          $i++;
      }
     exit;*/
 
     
  ?>

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Weekly Report</h3>
                  <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','weekly_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a>
                  <div class="clearfix"></div>
                </div>

                <div class="box-body">
                    <div class="row" style="margin-bottom:20px">
                       <div class="col-md-12">

                         <div id="perf_div"></div>
                          @columnchart('chartData', 'perf_div')
                       </div>
                    </div>
                    <div class="box">
                      <div class="row" style="margin-top:20px">
                        
                         <form class="form-horizontal" autocomplete="off">
                         
                           <label for="from_date" class="control-label col-md-2">Start Week </label> 
                           <div class="col-md-1 {{ session()->has('error') ? 'has-error' : '' }}">
                            <select name="from_week" class="form-control">
                              <option value="">Select</option>
                              @for($i = 1; $i <= $cWeek; $i++)
                                 <option value="{{$i}}" {{ (($i == $weekArray['from_week'])?'selected':'') }}>{{$i}}</option>
                              @endfor
                            </select>
                            @if(session()->has('error'))
                              <span class="help-block error">
                                  <strong>{{session()->get('error')}}</strong>
                              </span>
                                
                            @endif
                          </div>

                          
                           <label for="to_date" class="control-label col-md-2">End Week </label> 
                           <div class='col-md-1'>
                              <select name="to_week" class="form-control">
                                <option value="">Select</option>
                                @for($i = 1; $i <= $cWeek; $i++)
                                   <option value="{{$i}}" {{ (($i == $weekArray['to_week'])?'selected':'') }}>{{$i}}</option>
                                @endfor
                              </select>
                          </div>
                          <label for="to_date" class="control-label col-md-1">Year </label> 
                           <div class='col-md-2'>
                              <select name="year" class="form-control">
                                <option value="">Select</option>
                                @for($i = 2015; $i <= $cYear; $i++)
                                   <option value="{{$i}}" {{ (($i == $weekArray['year'])?'selected':'') }}>{{$i}}</option>
                                @endfor
                              </select>
                          </div>
                          <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-btn fa-search"></i> Search
                            </button>
                               
                          </div>
                          
                        </form>
                     </div>
                   </div>
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                              <th>Week</th>
                              <th>No. of Request received</th>
                              <th>No. of Request Quoted</th>
                              <th>No. of Lanes Quoted</th>
                              <!-- <th>No. of Inland Quoted</th> -->
                              <th>TAT (%)</th>
                              <th>Accuracy (%)</th>
                              <th>Errors</th>
                            </tr>
                         </thead>
                         <tbody>
                            <?php $i = $weekArray['from_week']; $sumNewReq = 0; $sumQuotes = 0; $sumlines = 0; $sumInland = 0; $sumErrors = 0;?>
                            @for($i = $weekArray['from_week']; $i <= $currentWeek; $i++ )
                              
                              <?php
                                $dateRange = App\Helpers\getWeek($i,$year);
                                $startDate = $dateRange['start'];
                                $endDate = $dateRange['end'];
                                $er[$i] = App\Model\WeeklyReport::errors($startDate,$endDate);
                              ?>
                            <tr>
                              <th>WK-{{ $i }}</th>
                              <td>{{ $newReq[$i] = App\Model\WeeklyReport::requestReceived($startDate,$endDate) }}
                               <?php $sumNewReq = $sumNewReq + $newReq[$i]; ?>
                              </td>
                              <td>{{ $quotes[$i] = App\Model\WeeklyReport::quotedRequest($startDate,$endDate) }}
                               <?php $sumQuotes = $sumQuotes + $quotes[$i]; ?>
                              </td>
                              <td>{{ $quotes[$i] = App\Model\WeeklyReport::quoteLineOffered($startDate,$endDate) }}
                               <?php $sumlines = $sumlines + $quotes[$i]; ?>
                              </td>
                              <?php /*/?>
                              <td>{{ $inland[$i] = App\Model\WeeklyReport::inLandQuoteOffered($startDate,$endDate) }}
                               <?php $sumInland = $sumInland + $inland[$i]; ?>
                              </td>
                              <?php /*/?>
                              <td>
                                <?php $tatPer[$i] = App\Model\WeeklyReport::missingTat($startDate,$endDate); ?>
                                {{ round($tatPer[$i],2) }}
                              </td>
                              <td>
                                <?php
                                if($quotes[$i] > 0)
                                  $acc = 100-(($er[$i]*100)/$quotes[$i]);
                                else
                                  $acc= 100;
                                ?>
                                {{round($acc,2)}}
                              </td>
                              <td>{{ $er[$i] }}
                               <?php $sumErrors = $sumErrors + $er[$i]; ?>
                               
                              </td>
                              </tr>
                             
                              <?php
                                /*$nextMonday = strtotime('+1 week', $nextMonday);
                                $nextFriday = strtotime('+1 week', $nextFriday);*/
                               // $i++;
                              ?>
                            @endfor
                              <tr>
                                <th>Overall Accuracy</th>
                                <td>{{ $sumNewReq }}</td>
                                <td>{{ $sumQuotes }}</td>
                                <td>{{ $sumlines }}</td>
                                <?php /*/?><td>{{ $sumInland }}</td><?php /*/?>
                                <td>100</td>
                                <td>
                                  <?php
                                  if($sumQuotes > 0)
                                    $total_acc = 100-(($sumErrors*100)/$sumQuotes);
                                  else
                                    $total_acc= 100;
                                  ?>
                                  {{round($total_acc,2)}}
                                </td>
                                <td>{{ $sumErrors }}</td>
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
<script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.js') }}"></script>
<script>
       
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
                 link.download = 'weekly_report_{{time()}}.xls';
                 link.target = '_blank';
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
          }
   })();

   
</script>

@endsection
