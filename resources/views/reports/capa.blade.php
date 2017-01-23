@extends('layouts.default')
   @section('title')
    CAPA Report
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
                  <h3 class="box-title">CAPA Report</h3>
                    <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','capa_report');"><i class="fa fa-file-excel-o"></i> Export to Excel</a>    
                   
                     <div class="clearfix"></div>
                </div>

                <div class="box-body">
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Sr. No.</th>     
                             <!-- <th>Mail Received DateTime</th>
                             <th>Request Number</th> -->
                             <th>Findings/Observation Description</th>
                             <th>Root Cause Analysis</th>
                             <th>Correction</th>
                             <th>Corrective Action</th>
                             <th>Preventive Action</th>
                             <th>Person responsible</th>
                             <th>Proposed Completion Date</th>                     
                             <th>Actual Completion Date</th>
                             <th>Status</th>
                            </tr>
                         </thead>
                         <tbody>
                          @foreach($capaData as $k => $capa)
                          <?php
                           // if()date('d M Y h:i A',strtotime($capa->audit_proposed_comp_date))
                          ?>
                            <tr>
                              <td>{{ $k+1 }}</td>
                              <td>{{ $capa->audit_error_description or $capa->error_description}}</td>
                              <td>{{ $capa->audit_root_cause or $capa->root_cause}}</td>
                              <td>{{ $capa->audit_correction or $capa->correction}}</td>
                              <td>{{ $capa->audit_corrective_action or $capa->corrective_action}}</td>
                              <td>{{ $capa->audit_preventive_action or $capa->preventive_action}}</td>
                              <td>{{ $capa->aq_user or $capa->pq_user}}</td>
                              <td>{{ $capa->audit_proposed_comp_date or $capa->proposed_comp_date}}</td>
                              <td>{{ $capa->audit_proposed_act_date or $capa->proposed_act_date}}</td>
                              <td>{{ $capa->aq_status_name or $capa->pq_status_name}}</td>
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
                 link.download = 'capa_report_{{time()}}.xls';
                 link.target = '_blank';
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
          }
   })();

   
</script>

@endsection

           