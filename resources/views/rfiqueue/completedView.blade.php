@extends('layouts.default')
   @section('title')
    Completed Queue
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
  @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Completed Queue</h3>
                  <!-- <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','completed_queue');"><i class="fa fa-file-excel-o"></i> Export to Excel</a> -->
                </div>
                <div class="box-body">
                    @if(session()->has('message'))
                    <div class="alert alert-success" role="alert">
                      <i class="fa fa-check"></i>
                        {{session()->get('message')}}
                    </div>
                    @endif
                    @if(session()->has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{session()->get('error')}}
                    </div>
                    @endif
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered table-striped table-hover" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Serial No.</th>     
                             <th>Week</th>
                             <th>Request No</th>
                             <th>Request Received Date</th>
                             <th>Region</th> 
                             <th>Priority</th> 
                             <th>No. Of Lanes</th> 
                             <th>No. Of Inlands</th> 
                             <th>RFI Raised By</th>
                             <th>RFIs</th>
                             <th>Account Name</th>
                             <th>Publisher</th> 
                             <th>RFI Resolved Time</th>
                             <th>Publish Start Time</th>
                             <th>Publish End Time</th>
                             <th>Auditor</th>
                             <th>Audit Start Time</th>
                             <th>Audit End Time</th>
                             <th>OOT Remark</th>
                             <th>Comments</th>
                             <th style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach($compData as $comp)
                           <tr class="">
                             <td style="width:1%">{{$i++}}</td>     
                             <td>{{date('W',strtotime($comp->mail_received_time))}}</td>
                             <td>{{$comp->request_no}}</td> 
                             <td>{{date('d M Y h:i A',strtotime($comp->mail_received_time))}}</td>
                             <td>{{$comp->region_name}}</td>                    
                             <td>{{$comp->priority_type}}</td>                    
                             <td>{{$comp->total_lane}}</td>                    
                             <td>{{$comp->no_of_inlands}}</td>                    
                             <td>{{ (($comp->audit_rfi_user)?$comp->audit_rfi_user:$comp->publish_rfi_user) }}</td>
                             <td>{{ (($comp->audit_rfi_type)?$comp->audit_rfi_type:$comp->process_rfi_type) }}</td>
                             <td>{{$comp->customer_name}}</td>                    
                             <td>{{$comp->publish_user}}</td>
                             <td>
                              @if(!empty($comp->audit_rfi_end_date) && $comp->audit_rfi_end_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($comp->audit_rfi_end_date))}}
                              @elseif(!empty($comp->rfi_end_date) && $comp->rfi_end_date!='0000-00-00 00:00:00')
                              {{date('d M Y h:i A',strtotime($comp->rfi_end_date))}}
                              @endif
                              </td>                    
                             <td>
                             @if(!empty($comp->publish_start_date) && $comp->publish_start_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($comp->publish_start_date))}}
                             @endif
                             </td>                    
                             <td>
                             @if(!empty($comp->publish_end_date) && $comp->publish_end_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($comp->publish_end_date))}}
                             @endif
                               
                             </td>                    
                             <td>{{$comp->audit_user}}</td>                    
                             <td>
                             @if(!empty($comp->audit_start_date) && $comp->audit_start_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($comp->audit_start_date))}}
                             @endif
                             </td>                    
                             <td>
                             @if(!empty($comp->audit_end_date) && $comp->audit_end_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($comp->audit_end_date))}} 
                             @endif
                             </td>                   
                             <td>{{ (($comp->aq_oot)?$comp->aq_oot:$comp->pq_oot) }}</td>
                             <td>{{ (($comp->aq_comment)?$comp->aq_comment:$comp->pq_comment) }}</td>
                             <td></td>
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

@endsection

@section('script')
 <script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
 <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
 <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
 <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
 <script>
   $(document).ready(function () {
      $(".table").DataTable({
        /*"columnDefs": [
            { "targets": [1,2,3,4,6,7,8,9,10,11,12,13,14,16,17], "orderable": false }
        ]*/
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive:true,
        dom: 'lBfrtip',                  
        buttons: ['excel'],
      });

    $('.buttons-excel').addClass('btn btn-info').removeClass('buttons-excel dt-button buttons-html5').prepend('<i class="fa fa-file-excel-o"></i>&nbsp');
    $('#example_length').addClass('col-md-2');
   });

   function openWindow(href){
    var newWin = window.open(href, 'User Process Form','left=200,top=80,width=1100,height=700,toolbar=1,resizable=0');
    newWin.document.close();
    return false;
   }
  
 </script>
@endsection

           