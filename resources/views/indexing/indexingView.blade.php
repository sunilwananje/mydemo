@extends('layouts.default')
   @section('title')
     Indexing
   @stop

  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
  @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">All Indexing</h3>
                        <a href="{{ route('indexing.create') }}" class="btn btn-primary pull-right" accesskey="n"><i class="fa fa-plus-circle"></i> Add Indexing</a>    
                   
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
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-hover" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Serial No.</th>     
                             <th>Mail Received Date & Time </th>
                             <th>TAT Remaining</th>
                             <th>Request No</th>
                             <th>Customer Name</th>
                             <th>Priority</th>
                             <th>Region</th>
                             <th>Office</th>                     
                             <th>Request Type</th>                  
                             <th style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach($indexings as $indexing)
                          <?php
                            $indexed_tat = App\Helpers\timeRemaining($indexing->indexing_tat);
                            
                          ?>
                           <tr>
                             <td style="width:1%">{{$i++}}</td>     
                             <td>{{date('d M Y h:i A',strtotime($indexing->mail_received_time))}}</td>
                             <td>{{$indexed_tat}}</td>                     
                             <td>{{$indexing->request_no}}</td>                     
                             <td>{{$indexing->customer_name}}</td>                     
                             <td>{{$indexing->priority_type}}</td>                     
                             <td>{{$indexing->region_name}}</td>
                             <td>{{$indexing->office_name}}</td>
                             <td>{{$indexing->request_type}}</td>                    
                             <td><a href="{{route('indexing.edit',$indexing->id)}}" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-xs btn-danger delete" type="button" data-toggle="modal" data-target="#confirmDelete" data-title="Delete" data-url="{{route('indexing.destroy',$indexing->id)}}">
                                <i class="fa fa fa-trash-o" ></i>
                                </button>
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
<form action="" method="post" id="confirm">
<div class="modal fade" id="confirmDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Delete Parmanently</h4>
      </div>
     
         {{ csrf_field() }}
         {{ method_field('DELETE') }}
          <div class="modal-body">
            <p>Are you sure you want to delete this indexing entry?</p>
          </div>
      
      <div class="modal-footer">
      <input type="hidden" class="route_url">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-default">Delete</a>
      </div>
    </div>
   </div>
  </div>
  </form>
@endsection
<!-- modal start  -->
 
<!-- modal end -->

@section('script')
 <script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
 <script>
   $(function () {
      $(".table").DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      });
   });
 </script>
@endsection

           