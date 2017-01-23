@extends('layouts.default')
   @section('title')
    Productivity Report
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

@section('content')
<?php $usersCount = count($userData);?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Productivity Report</h3>
                   
                    <div class="row">
                       <form class="form-horizontal" autocomplete="off">
                       <div class="col-md-4 {{ session()->has('error') ? 'has-error' : '' }}">
                         <label for="from_date" class="control-label">From Date </label> 
                          <div class="input-group date">
                          <input type='text' class="form-control datetimepicker" name="from_date" id="from_date" value="{{$datesArray['from_date'] or ''}}"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          @if(session()->has('error'))
                              <span class="help-block error">
                                  <strong>{{session()->get('error')}}</strong>
                              </span>
                                
                            @endif
                          </div>
                        </div>
                        <div class='col-md-4'>
                         <label for="to_date" class="control-label">To Date </label> 
                          <div class="input-group date">
                          <input type='text' class="form-control datetimepicker" name="to_date" id="to_date" value="{{ $datesArray['to_date'] or ''}}"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          </div>
                        </div>
                        <div class="col-md-4" style="margin-top: 27px;">
                          <button type="submit" class="btn btn-primary">
                              <i class="fa fa-btn fa-search"></i> Search
                          </button>
                             <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','productivity_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a>
                        </div>
                        
                        </form>
                   </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Sr. No.</th>   
                             <th>User</th>     
                             <th>Publish</th>     
                             <th>Audit</th>
                             <th>Total</th>
                             <th>MQC</th>
                             <th>No. of Working Days</th>
                             <th>Revised MQC As Per Working Days</th>
                             <th>Productivity%</th>
                             <th>No. Of Quotes Publish</th>
                             <th>No. Of Quotes Audit</th>
                             <th>No. Of Quotes</th>
                             <th>No. Of Errors</th>
                             <th>Accuracy %</th>
                            </tr>
                         </thead>
                         <tbody>
                          @foreach($userData as $k => $user)
                          <?php 

                            /*$publishCount = ((isset($publish[$user->name]))?$publish[$user->name]:0);
                            $auditCount = ((isset($audit[$user->name]))?$audit[$user->name]:0);
                            $publishCount = rand(10,15);
                            $auditCount = rand(16,20);
                            $total = $publishCount + $auditCount;
                            $sum = $sum + $total;
                            $mqc = $sum/$usersCount;*/

                            $wd = $finalData['workingDays'][$user->name];
                            $total = $finalData['total'][$user->name];
                            //$wd = rand(18,20);
                            $mqcWd = (round($finalData['mqc'],2)*$wd)/20;
                            //$mqcWd = (($mqcWd))
                            $prodPer =  (($mqcWd==0)?0:(($total*100)/round($mqcWd,2)));
                            
                            //echo '$wd ='.$wd.', $total='.$total.', $mqcWd='.$mqcWd.', $mqc='.$finalData['mqc'].', pp%='.$prodPer.'<br>';
                          ?>
                            <tr>
                              <td>{{ $k+1 }}</td>
                              <td>{{ $user->name or 'NA' }}</td>
                              <td>{{ $finalData['publish'][$user->name] }}</td>
                              <td>{{ $finalData['audit'][$user->name] }}</td>
                              <td>{{ $total }}</td>
                              @if($k==0)
                                 <td rowspan="{{$usersCount}}" class="text-center">{{ round($finalData['mqc'],2) }}</td>
                              @endif
                              <td>{{ $wd }}</td>
                              <td>{{ round($mqcWd,2) }}</td>
                              <td>{{ round($prodPer,2) }}</td>
                              <td>{{ $finalData['publishQuotes'][$user->name] }}</td>
                              <td>{{ $finalData['auditQuotes'][$user->name] }}</td>
                              <td>{{ $finalData['publishQuotes'][$user->name] + $finalData['auditQuotes'][$user->name] }}</td>
                              <td>{{ $finalData['error'][$user->name] }}</td>
                              <td>{{ round($finalData['accuracy'][$user->name],2) }}</td>
                            </tr>
                          @endforeach
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
                 link.download = 'productivity_report_{{time()}}.xls';
                 link.target = '_blank';
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
          }
   })();

   
</script>

@endsection

           