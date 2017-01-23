  @extends('layouts.default')
   @section('title')
    Publishing Queue
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <!-- <link rel="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui.css') }}">
     <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.css') }}">
    <style>
    .modal-dialog{
          overflow-y: initial !important
      }
      .modal-body{
          height: 600px;
          overflow-y: auto;
      }
    </style>
  @endsection
<?php $perArray = Session::get('permissions');?>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Publishing Queue</h3>
                    <!-- <div class="btn-group pull-right">
                      <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        Show Column
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a href="javascript:;" class="small" data-value="option1" ><input type="checkbox" class="check" value="0" id="sr_no" checked/>&nbsp;Serial No.</a></li>
                        <li><a href="javascript:;" class="small" data-value="option2" ><input type="checkbox" class="check" value="1" checked/>&nbsp;Week</a></li>
                        <li><a href="javascript:;" class="small" data-value="option3" ><input type="checkbox" class="check" value="2" checked/>&nbsp;Request No</a></li>
                        <li><a href="javascript:;" class="small" data-value="option4" ><input type="checkbox" class="check" value="3" checked/>&nbsp;Request Received Date</a></li>
                        <li><a href="javascript:;" class="small" data-value="option5" ><input type="checkbox" class="check" value="4" checked/>&nbsp;Region</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6" ><input type="checkbox" class="check" value="5" checked/>&nbsp;Priority</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6" ><input type="checkbox" class="check" value="6" checked>&nbsp;RFI Raised By</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="7" checked>&nbsp;RFIs</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6" ><input type="checkbox" class="check" value="8" checked>&nbsp;Account Name</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6" ><input type="checkbox" class="check" value="9" checked>&nbsp;Publisher</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="10" checked/>&nbsp;Publish Status</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="11" checked/>&nbsp;Publish Start Time</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="12" checked/>&nbsp;Publish End Time</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="13" checked/>&nbsp;OOT</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="14" checked/>&nbsp;OOT Remarks</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="15" checked/>&nbsp;TAT Pending Hrs</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="16" checked/>&nbsp;Actual TAT</a></li>
                        <li><a href="javascript:;" class="small" data-value="option6"><input type="checkbox" class="check" value="17" checked/>&nbsp;Comments</a></li>
                      </ul>
                    </div>
                     <div class="clearfix"></div> -->
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
                             <th>RFI Raised By</th>
                             <th>RFIs</th>
                             <th>Account Name</th>
                             <th>Publisher</th>
                             <th>Publish Status</th>
                             <th>Publish Start Time</th>
                             <th>Publish End Time</th>
                             <th>TAT Pending Hrs</th>
                             <th>Actual TAT</th>
                             <th>OOT</th>
                             <th>OOT Remarks</th>
                             
                             <th>Comments</th>                     
                             <th style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach($publishings as $publishing)
                          <?php
                            if($publishing->status_name === 'pending in'){
                               $indexed_tat = App\Helpers\stopTat($publishing->rfi_start_date,$publishing->indexing_tat);
                            }else{
                              $indexed_tat = App\Helpers\timeRemaining($publishing->indexing_tat);
                            }
                            $inv = App\Helpers\convertToHoursMins(abs($indexed_tat));
                            if($indexed_tat<0){
                              $inv = '-'.$inv;
                            }
                               
                            //$indexed_tat = absolute($indexed_tat);
                           // $indexed_tat = gmdate("H:i", ($indexed_tat * 60))
                          ?>
                           <tr class="{{(($indexed_tat<180)?'danger':'')}}">
                             <td style="width:1%">{{$i++}}</td>     
                             <td>{{date('W',strtotime($publishing->mail_received_time))}}</td>
                             <td>
                             @if(in_array('publishing.show',$perArray))
                               @if(($publishing->publish_by==Session::get('user_id')) || empty($publishing->publish_by) || (strtolower(Session::get('user_role'))=='admin'))
                                 <a href="javascript:;" id="{{route('publishing.show',$publishing->indexing_id)}}" onClick="return openWindow(this.id)" class="popup">{{$publishing->request_no}}</a>
                               @else
                                 {{$publishing->request_no}}
                               @endif
                             @else
                                {{$publishing->request_no}}
                             @endif
                             </td> 
                             <td>{{date('d M Y h:i A',strtotime($publishing->mail_received_time))}}</td>
                             <td>{{$publishing->region_name}}</td>                    
                             <td>{{$publishing->priority_type}}</td>
                             <td>{{$publishing->rfi_by_name}}</td>                    
                             <td>{{$publishing->rfi_name}}</td>                    
                             <td>{{$publishing->customer_name}}</td>                    
                             <td>{{$publishing->publish_by_name}}</td>                    
                             <td>{{strtoupper(($publishing->status_name)?$publishing->status_name:'to be started')}}</td>                    
                             <td>
                               @if(!empty($publishing->publish_start_date))
                                 {{date('d M Y h:i A',strtotime($publishing->publish_start_date))}}
                               @endif
                             </td>                    
                             <td>
                               @if(!empty($publishing->publish_end_date) && $publishing->publish_end_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($publishing->publish_end_date))}}
                               @endif
                             </td>
                             <td>{{ $inv }}</td>                    
                             <td>{{date('d M Y h:i A',strtotime($publishing->indexing_tat))}}</td>
                             <td> {{$publishing->comments}} </td>
                             <td> 

                              @if(Session::get('user_role')=='admin' && $indexed_tat<0)
                                <input type="checkbox" name="oot_check" value="{{$publishing->process_queue_id}}" class="oot_check" data-url="@if($publishing->process_queue_id){{url('ootEnable',$publishing->process_queue_id)}}@else 0 @endif" @if($publishing->oot==1) checked disabled @endif >      
                              @endif
                              </td>             
                             <td> {{$publishing->oot_remark}} </td>
                             <td>
                            
                             @if(Session::get('user_role')=='admin')
                               <a href="{{url('/publishing/changeStatus',$publishing->process_queue_id)}}" id="{{$publishing->process_queue_id}}" onclick="return confirm('Are sure to make it as new request?')" title="Change To New Request"><i class="fa fa-external-link-square"></i></a>&nbsp;&nbsp;
                               <a href="{{url('/publishing/delete',$publishing->indexing_id)}}" id="{{$publishing->process_queue_id}}" onclick="return confirm('Are sure to delete the request?')" title="Delete Request"><i class="fa fa-trash-o"></i></a>



                             @endif
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
<!-- modal start  -->
<div class="modal fade" id="ootConfirmModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">OOT Confirmation</h4>
      </div>
      <form action="" method="post" id="ootConfirm">  
      {{ csrf_field() }}
      <div class="modal-body">
        <div class="form-group" style="margin-bottom: 33px;">
          <label class="col-md-3">OOT Enabel</label>
          <div class="col-md-3">
            <select class="form-control" name="oot_status">
              <option value="0">N/A</option>
              <option value="1">Yes</option>
              <option value="2">No</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="pq_id" id="pq_id">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
     </form>

    </div>
   </div>
  </div>
  
  <!-- modal end -->
@endsection

@section('script')
<script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.js') }}"></script>
 <script>
   $(document).ready(function () {
    var table = $('.table').DataTable( {
                      //"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                      responsive:true,
                      dom: 'lBfrtip',                  
                      buttons: ['excel'],
                      
                    });
    $('.buttons-excel').addClass('btn btn-info').removeClass('buttons-excel dt-button buttons-html5').prepend('<i class="fa fa-file-excel-o"></i>&nbsp');
    $('#example_length').addClass('col-md-2');
  
    $('.oot_check').click(function() {
      var url = $(this).attr('data-url');
      if(url!=0){
        if(confirm("Are you sure to enable oot?")){
        if ($('input:checkbox[name=oot_check]:checked')) { 
            $(this).attr('disabled',true);
            //alert($(this).attr('data-url'));
            window.location.href = url;            
          }
       }else{
           $(this).attr('checked',false);
       }
     }else{
      $(this).removeAttr("checked");
       alert("Sorry! You can't make marked as oot for this request, You need to assigned user first");

     }
      
        
    });

    $('.check').on( 'click', function (e) {
      $(".table").css('width','0px');
       var column = $(this).val();
       if(!$(this).is(':checked')){
        // Toggle the visibility
        table.column( column ).visible( false );
        //column.visible(! column.visible() );
       }else{
        table.column( column ).visible( true );
       }
        // Get the column API object
        
    } );

    });
   function openWindow(href){
    //alert(href);
    $.ajax({
      type: 'get',
      url: href,
      data:{action:'publish'},
      dataType: 'html',
      success: function(msg) {
        $('#upfModalContainer').html(msg);
        $('#upfModal').modal('show');
      },
      error: function (xhr, status, err) {
           //alert(xhr.status+' '+err);
           if(xhr.status == 401){
              alert("Your session is expired! Please login again");
              window.location.href = "{{url('/login')}}";
           }
                       
        },
      beforeSend: function(){
          $('#loaderDiv').show();

      },
      complete: function(){
          $('#loaderDiv').hide();
      }
    });
   }
   /*function openWindow(href){
    var newWin = window.open(href, 'User Process Form','left=200,top=80,width=1000,height=600+,toolbar=1,resizable=0');
    newWin.document.close();
    return false;
   }*/
 </script>
 <div id="upfModalContainer"></div>
@endsection

           