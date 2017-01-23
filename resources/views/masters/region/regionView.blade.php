@extends('layouts.default')
   @section('title')
    Regions
  @stop
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">All Regions</h3>
                        <a href="{{ route('region.create') }}" class="btn btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add Region</a>    
                   
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
                             <th>Region Name</th>
                             <th>Average volume</th>
                             <th>Region Abbreviation</th>                     
                             <th colspan="2" style="width:10%; text-align: center">Action</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach(\App\Model\Region::all() as $region)
                           <tr>
                             <td>{{$i++}}</td>     
                             <td>{{$region->name}}</td>
                             <td>{{$region->region_volume}}</td>                   
                             <td>{{$region->region_abbr}}</td>                   
                             <td><a href="{{route('region.edit',$region->id)}}" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-xs btn-danger delete" type="button" data-toggle="modal" data-target="#confirmDelete" data-title="Delete" data-url="{{route('region.destroy',$region->id)}}">
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
            <p>Are you sure you want to delete this region?</p>
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

           