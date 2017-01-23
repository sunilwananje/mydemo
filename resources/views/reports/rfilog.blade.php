@extends('layouts.default')
   @section('title')
    RFI Log Report
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

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">RFI Log Report</h3>
                  <?php /*/?>
                     <div class="row">
                       <form class="form-horizontal" autocomplete="off">
                       <div class="col-md-4 {{ session()->has('error') ? 'has-error' : '' }}" >
                         <label for="from_date" class="control-label">From Date </label> 
                          <div class="input-group date">
                          <input type='text' class="form-control datetimepicker" name="from_date" id="from_date" value="{{ $dateArray['from_date'] or ''}}"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          </div>
                          @if(session()->has('error'))
                              <span class="help-block error">
                                  <strong>{{session()->get('error')}}</strong>
                              </span>
                                
                            @endif
                        </div>
                        <div class='col-md-4'>
                         <label for="to_date" class="control-label">To Date </label> 
                          <div class="input-group date">
                          <input type='text' class="form-control datetimepicker" name="to_date" id="to_date" value="{{ $dateArray['to_date'] or ''}}"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          </div>
                        </div>
                        <div class="col-md-4" style="margin-top: 27px;">
                          <button type="submit" class="btn btn-primary">
                              <i class="fa fa-btn fa-search"></i> Search
                          </button>
                            
                        </div>
                        
                        </form>
                   </div>
                   <?php /*/?>
                   <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','rfilog_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a>
                    <div class="clearfix"></div>
                </div>

                <div class="box-body">
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Sr. No.</th>   
                             <th>Unique No.</th>     
                             <th>DateTime</th>
                             <th>Customer Name</th>
                             <th>Region</th>
                             <th>Comment</th>
                             <th>Status</th>
                            </tr>
                         </thead>
                         <tbody>
                          @forelse ($rfiData as $k => $rfi)
                          <?php
                           if($rfi->aq_status_name){
                            $status_name = $rfi->aq_status_name;
                           }else{
                            $status_name = $rfi->pq_status_name;
                           }
                           if($status_name == 'pending in'){
                             $status = 'Open';
                           }else{
                            $status = 'Closed';
                           }
                          ?>
                            <tr>
                              <td>{{ $k+1 }}</td>
                              <td>{{ $rfi->request_no}}</td>
                              <td>{{ date('d-m-Y H:i',strtotime($rfi->mail_received_time)) }}</td>
                              <td>{{ $rfi->customer_name }}</td>
                              <td>{{ $rfi->region_name }}</td>
                              <td>{{ $rfi->audit_rfi_description or $rfi->rfi_description}}</td>
                              <td>{{ $status }}</td>
                            </tr>
                           @empty
                            <tr>
                              <th colspan="7" style="text-align:center"> No records found</th>
                            </tr>
                         @endforelse
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
     /*$(document).ready(function(){
       $(".table").DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      });
     });*/  
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
                 link.download = 'rfilog_report_{{time()}}.xls';
                 link.target = '_blank';
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
          }
   })();

   
</script>

@endsection

           