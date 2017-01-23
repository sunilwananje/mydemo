@extends('layouts.default')
 @section('title')
  Auditing Queue
@stop
@section('styles')
   <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
   <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui.css') }}">
   <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.css') }}">
    <style>
      .modal-dialog{
          overflow-y: initial !important
      }
      .modal-body{
          height: 450px;
          overflow-y: auto;
      }
    </style>
@endsection
<?php $perArray = Session::get('permissions'); ?>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Auditing Queue</h3>
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
                             <th>Index Type</th>
                             <th>RFI Raised By</th>
                             <th>RFIs</th> 
                             <th>Priority</th>
                             <th>SQ No.</th>
                             <th>Account Name</th>
                             <th>Publisher</th>
                             <th>Publish Status</th>
                             <th>Auditor</th>
                             <th>Audit Status</th>
                             <th>Audit Start Time</th>
                             <th>Audit End Time</th>
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
                         @foreach($auditings as $auditing)
                          <?php
                          if($auditing->audit_status_name === 'pending in'){
                             $indexed_tat = App\Helpers\stopTat($auditing->audit_rfi_start_date,$auditing->indexing_tat);
                          }else{
                            $indexed_tat = App\Helpers\timeRemaining($auditing->indexing_tat);
                          }
                          $inv = App\Helpers\convertToHoursMins(abs($indexed_tat));
                          if($indexed_tat < 0){
                            $inv = '-'.$inv;
                          }
                          //echo 'rfi=>'.$auditing->audit_rfi_start_date.', tat=>'.$auditing->indexing_tat.'<br>';
                          /*if($indexed_tat['h']<0){
                              $time = $indexed_tat['h'].' hrs '.$indexed_tat['m'].' min';//'0 hrs';
                          }else{
                              $time = $indexed_tat['h'].' hrs '.$indexed_tat['m'].' min';
                          } */
                           /*if($auditing->oot==0){
                            $msg = "N/A";
                           }elseif($auditing->oot==1){
                            $msg = "Yes";
                           }elseif($auditing->oot==2){
                            $msg = "No";
                           }else{
                            $msg ='';
                           }*/
                          ?>
                           <tr class="{{(($indexed_tat<180)?'danger':'')}}">
                             <td style="width:1%">{{$i++}}</td>     
                             <td>{{date('W',strtotime($auditing->mail_received_time))}}</td>
                             <td>
                             
                               @if(empty($auditing->audit_by))
                                 <a href="javascript:;" id="{{route('auditing.show',$auditing->id)}}" rel="apfCreateModal" onClick="return openWindow(this.id,this.rel)" class="popup">{{$auditing->request_no}}</a>
                               @elseif($auditing->audit_by==Session::get('user_id') || (strtolower(Session::get('user_role'))=='admin'))
                                 <a href="javascript:;" id="{{route('auditing.edit',$auditing->id)}}" rel="apfEditModal" onClick="return openWindow(this.id,this.rel)" class="popup">{{$auditing->request_no}}</a>
                               @else
                                 {{$auditing->request_no}}
                               @endif
                             </td> 
                             <td>{{date('d M Y h:i A',strtotime($auditing->mail_received_time))}}</td>
                             <td>{{$auditing->region_name}}</td>                    
                             <td>{{$auditing->request_type}}</td>                    
                             <td>{{$auditing->rfi_questioner_name}}</td>                    
                             <td>{{$auditing->audit_rfi_name}}</td>
                             <td>{{$auditing->priority_type}}</td>                    
                             <td>{{$auditing->sq_no}}</td>                    
                             <td>{{$auditing->customer_name}}</td>                    
                             <td>{{$auditing->publisher_name}}</td>                    
                             <td>{{strtoupper($auditing->publish_status_name)}}</td>
                             <td>{{$auditing->auditor_name}}</td>                   
                             <td>{{strtoupper(($auditing->audit_status_name)?$auditing->audit_status_name:'to be started')}}</td>                    
                             <td>
                               @if(!empty($auditing->audit_start_date) && $auditing->audit_start_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($auditing->audit_start_date))}}
                               @endif
                             </td>                    
                             <td>
                               @if(!empty($auditing->audit_end_date) && $auditing->audit_end_date!='0000-00-00 00:00:00')
                                 {{date('d M Y h:i A',strtotime($auditing->audit_end_date))}}
                               @endif
                             </td>
                             <td style="">{{ $inv }}</td>                    
                             <td>{{date('d M Y h:i A',strtotime($auditing->indexing_tat))}}</td>
                             
                             <td> @if(Session::get('user_role')=='admin' && $indexed_tat<0)
                                <input type="checkbox" name="oot_check" value="{{$auditing->id}}" class="oot_check" data-url="{{url('ootAuditEnable',$auditing->id)}}" @if($auditing->oot==1) checked disabled @endif >      
                              @endif </td>                   
                             <td> {{$auditing->oot_remark}} </td>  
                             <td> {{$auditing->comments}} </td>
                             <td>                 
                             <?php /*/ ?>
                             {{(($indexed_tat<180)?'color:#FF0000':'color:#00A65A')}}
                             
                             @if(Session::get('user_role')=='admin' && $indexed_tat<0)
                               <a href="javascript:;" class="oot" id="{{$auditing->id}}" data-url="{{url('ootAuditEnable')}}"> OOT Enable</a>
                             @endif
                             
                             <?php /*/ ?>
                               @if(Session::get('user_role')=='admin')
                              <a href="{{url('/auditing/changeStatus',$auditing->id)}}" id="{{$auditing->id}}" onclick="return confirm('Are sure to make it as new request?')" title="Change To New Request"><i class="fa fa-external-link-square"></i></a>&nbsp;&nbsp;
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

      $('.oot_check').click(function() {
        if(confirm("Are you sure to enable oot?")){
          if ($('input:checkbox[name=oot_check]:checked')) { 
              $(this).attr('disabled',true);
              window.location.href = $(this).attr('data-url');            
            }
         }else{
          $(this).attr('checked',false);
         }
    });

   });
   function openWindow(href,modal){
    //alert(modal);
    $.ajax({
      type: 'get',
      url: href,
      data:{action:'audit'},
      dataType: 'html',
      success: function(msg) {
        //alert(msg);
        $('#apfModalContainer').html(msg);
        $('#'+modal).modal('show');
      },
      error: function (xhr, status, err) {
           alert(xhr.status+' '+err);
           if(xhr.status == 401){
              alert("Your session is expired! Please login again");
              window.location.href = "{{url('/login')}}";
           }
                       
        },
      beforeSend: function(){
          $('#loaderDiv').show();

      },
      complete: function(xhr, status){
          $('#loaderDiv').hide();
      }
    });
   }

   /*function openWindow(href,modal){
    var newWin = window.open(href, 'User Process Form','left=200,top=80,width=1000,height=600,toolbar=1,resizable=0');

    newWin.document.close();
    return false;
   }*/
 </script>
 <div id="apfModalContainer"></div>
@endsection

           