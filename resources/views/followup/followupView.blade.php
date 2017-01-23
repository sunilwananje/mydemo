@extends('layouts.default')
   @section('title')
    Follow Up Queue
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
  @endsection
<?php $statusData = \App\Model\Status::whereIn('status_name',['sent to customer','sent to inside sales'])->get();?>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Follow Up Queue</h3>
                  <span class="pull-right">
                  <a href="{{ url('mail/reminder') }}" class="btn btn-primary">Send Reminder Mail</a>
                  <!--&nbsp; <a href="javascript:;" class="btn btn-info " onclick="exportThisWithParameter('example','followup_list');"><i class="fa fa-file-excel-o"></i> Export to Excel</a> -->
                  </span>          
                     <div class="clearfix"></div>
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
                             <th>SQ No</th>
                             <th>Account Name</th>
                             <th>Publisher</th>
                             <th>Status</th>
                             <th>Reminder1</th>
                             <th>Reminder2</th>
                             <th>Reminder1 Sent</th>
                             <th>Reminder2 Sent</th>
                             <!-- <th>Reminder1 Actual Sent Date</th>
                             <th>Reminder2 Actual Sent Date</th> -->
                             <th>Final Status</th>
                             <th style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach($followUpData as $followup)
                           <tr class="">
                             <td style="width:1%">{{$i++}}</td>     
                             <td>{{date('W',strtotime($followup->mail_received_time))}}</td>
                             <td>{{$followup->request_no}}</td> 
                             <td>{{date('d M Y h:i A',strtotime($followup->mail_received_time))}}</td>
                             <td>{{$followup->region_name}}</td>                    
                             <td>{{$followup->priority_type}}</td>                   
                             <td>{{$followup->sq_no}}</td>                   
                             <td>{{$followup->customer_name}}</td>                    
                             <td>{{$followup->publisher_name}}</td>                    
                             <td>
                              @if($followup->audit_status_name)
                               {{ strtoupper($followup->audit_status_name) }}
                              @else
                               {{ strtoupper($followup->pq_status_name) }}
                              @endif
                             </td>                    
                             <td>
                             @if($followup->reminder_1)
                               {{date('d M Y h:i A',strtotime($followup->reminder_1))}}
                             @else
                               {{date('d M Y h:i A',strtotime($followup->pq_reminder_1))}}
                             @endif
                               </td>
                             <td>
                              @if($followup->reminder_2)
                               {{date('d M Y h:i A',strtotime($followup->reminder_2))}}
                              @else
                               {{date('d M Y h:i A',strtotime($followup->pq_reminder_2))}}
                              @endif
                             </td>

                             <td>{{$followup->reminder1_sent or $followup->pq_reminder1_sent}}</td>                    
                             <td>{{$followup->reminder2_sent or $followup->pq_reminder2_sent}}</td>                  
                             <?php /*/ ?><td>{{$followup->reminder1_actual_sent}}</td>                    
                             <td>{{$followup->reminder2_actual_sent}}</td><?php /*/ ?>
                             <td>{{$followup->final_status}}</td>                    
                             <td>
                             <!-- if($followup->audit_status_name != 'sent to customer' && $followup->audit_status_name != 'sent to inside sales') -->
                               <select name="status_id" id="" class="form-control status_id">
                                  <option value="">Select</option>
                                  @foreach($statusData as $status)
                                    <option value="{{$status->id}}" data-pq="{{$followup->process_queue_id}}" data-aq="{{$followup->audit_queue_id}}" data-in="{{$followup->indexing_id}}">{{strtoupper($status->status_name)}}</option>
                                  @endforeach
                                </select>
                             
                             </td>
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
<div class="modal fade" id="pricerModal" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Pricer Database</h4>
      </div>
      <form action="{{route('pricer.store')}}" method="post" id="pricerForm" class="form-horizontal" role="form">  
      {{ csrf_field() }}
      <div class="modal-body">
        <div class="form-group">
          <label class="col-md-3">POL Port*</label>
          <div class="col-md-6">
            <input type="text" name="pol_port" class="form-control capital-letter" style="text-transform: uppercase;">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3">POL Region*</label>
          <div class="col-md-6">
            <input type="text" name="pol_region" class="form-control capital-letter" style="text-transform: uppercase;">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3">POD Port*</label>
          <div class="col-md-6">
            <input type="text" name="pod_port" class="form-control capital-letter" style="text-transform: uppercase;">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3">POD Region*</label>
          <div class="col-md-6">
            <input type="text" name="pod_region" class="form-control capital-letter" style="text-transform: uppercase;">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3">Pricer Name*</label>
          <div class="col-md-6">
            <input type="text" name="pricer_name" class="form-control capital-letter" style="text-transform: uppercase;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="process_queue_id" id="pq_id">
        <input type="hidden" name="audit_queue_id" id="aq_id">
        <input type="hidden" name="indexing_id" id="indexing_id">
        <input type="hidden" name="status" id="status">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
     </form>

    </div>
   </div>
  </div>
  <input type="hidden" id="url" value="{{ url('/pricer/updateStatus') }}">
@endsection
<!-- modal start  -->


<!-- modal end -->
@section('script')
 <!-- <script src="//code.jquery.com/jquery-1.12.3.js"></script> -->
 <!-- <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script> -->
 <script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
 <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
 <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
 <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
 <script src="{{ asset('/assets/dist/js/jquery.validate.js') }}"></script>
 <script src="{{ asset('/assets/dist/js/validation.js') }}"></script>
 <script>
   $(document).ready(function () {
      $(".table").DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive:true,
        dom: 'lBfrtip',                  
         buttons: [ {
            extend: 'excelHtml5',
            exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 ]
                },
            
        } ]
      });

      $('.buttons-excel').addClass('btn btn-info').removeClass('buttons-excel dt-button buttons-html5').prepend('<i class="fa fa-file-excel-o"></i>&nbsp');
      $('#example_length').addClass('col-md-2');

      $(document).on('change','.status_id',function(){
        //alert("g");
        var val = $(this).find('option:selected').text().toLowerCase();
        var status = $(this).val();
        var pq_id = $(this).find('option:selected').attr('data-pq');
        var aq_id = $(this).find('option:selected').attr('data-aq');
        var indexing_id = $(this).find('option:selected').attr('data-in');
        if(val=='sent to customer'){
          $('#pricerForm')[0].reset();
          $('#pq_id').val(pq_id);
          $('#aq_id').val(aq_id);
          $('#indexing_id').val(indexing_id);
          $('#status').val(status);
          $('#pricerModal').modal('show');
        }else{
          var url = $('#url').val();
           $.ajax({
              type: 'post',
              url: url,
              data:{status:status,aq_id:aq_id},
              dataType: 'html',
              success: function(msg) {
                window.location.href = location.pathname;
              },
              error: function (xhr, status, err) {
                   //alert(xhr.status+' '+err);
                   if(xhr.status == 401){
                      alert("Your session is expired! Please login again");
                     // window.location.href = "{{url('/login')}}";
                   }
                               
                },
              beforeSend: function(){
                  $('#loaderDiv').show();

              },
              complete: function(){
                  $('#loaderDiv').hide();
              }
            });
          /*$('#loaderDiv').show();
          $.post(url, {status:status,aq_id:aq_id}, function(result){
           // window.location.reload();
            window.location.href = location.pathname;
            $('#loaderDiv').hide();
          });*/
          
        }
      });
  });
  
 </script>
@endsection

           