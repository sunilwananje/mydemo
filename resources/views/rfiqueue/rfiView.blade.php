@extends('layouts.default')
   @section('title')
    RFI Queue
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
          height: 600px;
          overflow-y: auto;
      }
    </style>
  @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">RFI Queue</h3>
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
                             <th>Customer</th>
                             <th>Region</th> 
                             <th>RFI Raised By</th>
                             <th>RFI Type</th>
                             <th>RFI Description</th>
                             <!-- <td>Remaining TAT</td> --> 
                             <th>RFI Resolution</th>
                             <th>RFI Resolution Comments</th>
                             <th>Status</th>
                             <th style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; //echo '<pre>'; print_r($publishings); echo '</pre>'; ?>
                         @foreach($rfiData as $rfi)
                          <?php
                            if($rfi->rfi_from=='pq'){
                              $modal='upfModal';
                              $rfi_by=$rfi->publish_rfi_user;
                              $rfi_type=$rfi->process_rfi_type;
                              $rfi_description=$rfi->rfi_description;
                              $route=route('publishing.show',$rfi->indexing_id);
                              $rfi_end_date=$rfi->rfi_end_date;
                              $rfi_comment=$rfi->rfi_comment;
                              $rfi_status=$rfi->process_status;
                            }else{
                              $modal='apfEditModal';
                              $rfi_by=$rfi->audit_rfi_user;
                              $rfi_end_date=$rfi->audit_rfi_end_date;
                              $rfi_comment=$rfi->audit_rfi_comment;
                              $rfi_type=$rfi->audit_rfi_type;
                              $rfi_description=$rfi->audit_rfi_description;
                              $rfi_status=$rfi->audit_status;
                              $route=route('auditing.edit',$rfi->audit_queue_id);
                            }
                            if($rfi->audit_rfi_start_date){
                              $rfi_std = $rfi->audit_rfi_start_date;
                            }else{
                              $rfi_std = $rfi->rfi_start_date;
                            }
                            $indexed_tat = App\Helpers\stopTat($rfi_std,$rfi->indexing_tat);
                          ?>
                           <tr class="">
                             <td style="width:1%">{{$i++}}</td>     
                             <td>{{date('W',strtotime($rfi->mail_received_time))}}</td>
                             <td><a href="javascript:;" rel="{{ $modal }}" id="{{$route}}" onClick="return openWindow(this.id,this.rel,{{$rfi->id}})" class="popup">{{$rfi->request_no}}</a></td> 
                             <td>{{date('d M Y h:i A',strtotime($rfi->mail_received_time))}}</td>
                             <td>{{$rfi->customer_name}}</td>
                             <td>{{$rfi->region_name}}</td>                    
                             <td>{{$rfi_by}}</td>
                             <td>{{$rfi_type}}</td>                    
                             <td>{{$rfi_description}}</td>                    
                             <?php /*/?><td style="{{(($indexed_tat<180)?'color:#FF0000':'color:#00A65A')}}">{{$indexed_tat}} min</td><?php /*/?>                    
                             <td>@if($rfi_end_date!='0000-00-00 00:00:00' && !empty($rfi_end_date)){{date('d M Y h:i A',strtotime($rfi_end_date))}} @endif </td>                    
                             <td>{{$rfi_comment}}</td>                    
                             <td>{{strtoupper($rfi_status)}}</td>                    
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
<!--rfiModal-->
<div class="modal fade" id="rfiModal" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">RFI Resolved</h4>
      </div>
      <form class="form-horizontal" role="form" method="POST" action="" id="apfForm" autocomplete="off" >
        <div class="modal-body">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="box-body">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Request Details</h3>
              </div>
              <div class="box-body">
                  <div class="form-group">
                      <label for="rfi_end_date" class="col-md-3 control-label">RFI End Date <span style="color:#FF0000">*</span></label>
                      <div class="input-group date col-md-3">
                          <input type='text' class="form-control" name="rfi_end_date" id="rfi_end_date" value="{{old('rfi_end_date')}}" readonly />
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          
                          <input type='hidden' name="indexing_id" value=""/>
                          <input type='hidden' name="publishing_id" value=""/>
                          <input type='hidden' name="auditing_id" value=""/>
                      </div>                      
                  </div>

                   <div class="form-group">
                         <label for="rfi_comment" class="col-md-3 control-label">RFI Resolved Comment</label>
                          <div class="col-md-3">
                          <textarea name="rfi_comment" id="rfi_comment" class="form-control"></textarea>
                         </div>
                   </div>
                  </div>
                </div><!--End Section Request Details-->
                
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
          <i class="fa fa-btn fa-floppy-o"></i> Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
   </div>
  </div>
<!--rfiModal-->
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
   });
   function openWindow(href,modal,id){
    //alert(modal);
    $.ajax({
      type: 'get',
      url: href,
      data:{action:'rfi','rfi_id':id},
      dataType: 'html',
      success: function(msg) {
        //alert(msg);
        $('#modalContainer').html(msg);
        $('#'+modal).modal('show');
      },
      beforeSend: function(){
          $('#loaderDiv').show();
      },
      complete: function(){
          $('#loaderDiv').hide();
      }
    });
   }
   /*function openWindow(href,modal){
    var newWin = window.open(href, 'User Process Form','left=200,top=80,width=1100,height=700,toolbar=1,resizable=0');
    newWin.document.close();
    return false;
   }*/
 </script>
 <div id="modalContainer"></div>
@endsection

           