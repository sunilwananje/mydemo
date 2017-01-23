@extends('layouts.default')
  @section('title')
    Container Types
  @stop
  @section('page-header')
   <!-- <section class="content-header">
      <h1>
        Container Types
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Container Type List</li>
      </ol>
    </section> -->
  @stop
@section('content')
<?php $permissionArray = Session::get('permissions'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box">

                <div class="box-header with-border">
                  <h3 class="box-title"> All Container Types </h3>
                        <a href="{{ route('containerType.create') }}" class="btn btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add Container Type</a>       
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
                        <table class="table table-responsive table-striped table-bordered table-hover no-margin">
                          <thead>
                            <tr>
                             <th>Serial No.</th>     
                             <th>Container Type Name</th>
                             <th style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach(\App\Model\ContainerType::all() as $containerType)
                           <tr>
                             <td>{{$i++}}</td>     
                             <td>{{$containerType->name}}</td>
                             <td>
                                @if(in_array('containerType.edit',$permissionArray))
                                <a href="{{route('containerType.edit',$containerType->id)}}" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                @endif
                                @if(in_array('containerType.destroy',$permissionArray))
                                <button class="btn btn-xs btn-danger delete" type="button" data-toggle="modal" data-target="#confirmDelete" data-title="Delete" data-url="{{route('containerType.destroy',$containerType->id)}}">
                                <i class="fa fa fa-trash-o" ></i>
                                </button>
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
            <p>Are you sure you want to delete this container type?</p>
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

           