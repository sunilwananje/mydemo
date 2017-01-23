@extends('layouts.default')
   @section('title')
    Monthly Report
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <!-- <link rel="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui.css') }}">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.css') }}">
    <!--  <style>
       .text-center{
         text-align:center !important;
         vertical-align:middle !important;
       }
       .cell-bg{
         background: #3c8dbc;color: white !important;
       }
     </style> -->
  @endsection
 
@section('content')

<?php
  
  $monthArray = array(1 => 'January', 2 =>'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

  if(empty($monthData['from_month']) || empty($monthData['to_month']) || empty($monthData['year'])){
        $monthData['from_month']  = 1;
        $monthData['to_month']    = date('m'); 
        $monthData['year'] = date('Y');
     }
      $fromMonth              = $monthData['from_month'];
      $toMonth    = $monthData['to_month'];
      $year    = $monthData['year'];
      $cYear      = date('Y');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Monthly Report</h3>
                  <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','monthly_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a>    
                   
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
                         
                           <label for="from_date" class="control-label col-md-1">Start Month </label> 
                           <div class="col-md-2 {{ session()->has('error') ? 'has-error' : '' }}">
                            <select name="from_month" class="form-control">
                              <option value="">Select</option>
                              @foreach($monthArray as $k => $month)
                                 <option value="{{$k}}" {{ (($k == $monthData['from_month'])?'selected':'') }}>{{$month}}</option>
                              @endforeach
                            </select>
                            @if(session()->has('error'))
                              <span class="help-block error">
                                  <strong>{{session()->get('error')}}</strong>
                              </span>
                                
                            @endif
                          </div>

                          
                           <label for="to_date" class="control-label col-md-1">End Month </label> 
                           <div class="col-md-2">
                               <select name="to_month" class="form-control">
                                <option value="">Select</option>
                                @foreach($monthArray as $k => $month)
                                   <option value="{{$k}}" {{ (($k == $monthData['to_month'])?'selected':'') }}>{{$month}}</option>
                                @endforeach
                              </select>
                          </div>

                          <label for="to_date" class="control-label col-md-1">Year </label> 
                           <div class='col-md-2'>
                              <select name="year" class="form-control">
                                <option value="">Select</option>
                                @for($i = 2015; $i <= $cYear; $i++)
                                   <option value="{{$i}}" {{ (($i == $monthData['year'])?'selected':'') }}>{{$i}}</option>
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
                              <th>Month</th>
                              <th>No. of Request Quoted</th>
                              <th>No. of Lanes Quoted</th>
                              <th>TAT (%)</th>
                              <th>Accuracy (%)</th>
                            </tr>
                         </thead>
                         <tbody>
                            <?php $i = 1; $sumNewReq = 0; $sumQuotes = 0; $sumInland = 0; $sumErrors = 0; ?>
                            @for($i = $fromMonth; $i <= $toMonth; $i++)
                            <?php
                              $lastdateofmonth=date('t',$i);
                              $startDate = date('Y-m-d',strtotime($year.'-'.$i.'-01')) ;
                              $endDate = date('Y-m-t',strtotime($startDate));
                              $er[$i] = App\Model\WeeklyReport::errors($startDate,$endDate);
                            ?>
                            <tr>
                              <th>{{ date('M',strtotime($startDate)).'-'.date('Y',strtotime($startDate)) }}</th>
                              <td>{{ $quotes[$i] = App\Model\WeeklyReport::quotedRequest($startDate,$endDate) }}
                               <?php $sumQuotes = $sumQuotes + $quotes[$i]; ?>
                              </td>
                              <td>{{ $inland[$i] = App\Model\WeeklyReport::quoteLineOffered($startDate,$endDate) }}
                               <?php $sumInland = $sumInland + $inland[$i]; ?>
                              </td>
                              <td>
                               <?php
                                if($i <= date('m')){
                                 $tatPer[$i] = App\Model\WeeklyReport::missingTat($startDate,$endDate);
                                }else{
                                  $tatPer[$i] = 0;
                                }
                               ?>
                                {{ round($tatPer[$i],2) }}</td>
                              <td>
                                <?php
                                if($quotes[$i] > 0)
                                  $acc = 100-(($er[$i]*100)/$quotes[$i]);
                                else
                                  $acc= 0;
                                ?>
                                {{round($acc,2)}}
                              </td>
                              
                              
                              </tr>
                            @endfor
                              <tr>
                                <th>Overall Accuracy</th>
                                <td>{{ $sumQuotes }}</td>
                                <td>{{ $sumInland }}</td>
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
                 link.download = 'monthly_report_{{time()}}.xls';
                 link.target = '_blank';
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
          }
   })();

   
</script>

@endsection

           