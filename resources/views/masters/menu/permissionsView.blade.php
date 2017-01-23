@extends('layouts.default')
  @section('title')
    All Permisions
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
  @endsection

  @section('page-header')
   <!-- <section class="content-header">
      <h1>
        All Menu List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section> -->
  @stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">All Permission</h3>
                  <a href="{{ url('/permission/sync') }}" class="btn btn-primary pull-right"><i class="fa fa-retweet"></i> Sync Permission</a>    
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
                        <table id="rfi_list" class="table table-responsive table-striped table-bordered table-hover no-margin">
                          <thead>
                            <tr>
                             <th>Serial No.</th>     
                             <th>Permission Name</th>
                             <th>Permision Path</th>
                             <th>Status</th>
                             <th>Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach(\App\Model\Permission::all() as $per)
                           <tr>
                             <td>{{$i++}}</td>     
                             <td>{{$per->display_name}}</td>
                             <td>{{$per->name}}</td>
                             <td>{{(($per->status == 1)?'Enable':'Disable')}}</td>
                             <td>
                               @if($per->status == 1)
                                <a href="{{url('/permission/status',[$per->id,0])}}" title="Make Disable" style="color:#0000FF"><i class="fa fa-check" ></i></a>
                               @else
                                <a href="{{url('/permission/status',[$per->id,1])}}" title="Make Enable" style="color:#FF0000"><i class="fa fa-ban" ></i></a>
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

@endsection
<!-- modal start  -->
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
            <p>Are you sure you want to delete this?</p>
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
<!-- modal end -->

@section('script')
 <script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
 <script>
   $(function () {
      $(".table").DataTable();
   });
 </script>
@endsection

           