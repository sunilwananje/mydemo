@extends('layouts.default')
   @section('title')
    Admin Tracker Data
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
                <div class="box-header with-border">
                  <h3 class="box-title">Admin Tracker Data</h3>
                       <!--  <a href="javascript:;" class="btn btn-info pull-right" onclick="exportThisWithParameter('example','all_live_data');"><i class="fa fa-file-excel-o"></i> Export to Excel</a> -->
                   
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
                      <div class="row margin-bottom">
                        <form action="">
                          <div class="col-md-3"></div>
                          <div class="col-md-3">
                            <select class="form-control" name="filter">
                              <option value="all">All Data</option>
                              <option value="live">Live Data</option>
                            </select>
                          </div>
                          <div class="col-md-3">
                            <button type="submit" name="search" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                          </div>
                        </form>
                      </div>
                        <table id="example" class="table table-striped table-bordered table-hover no-margin" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Serial No.</th>    
                             <th>Request No</th>
                             <th>Publisher</th>
                             <th>Status</th> 
                             <th>Auditor</th>
                             <th>Service Type</th> 
                             <th>No. of Lanes</th>
                             <th>No. of Inlands</th>
                             <th>Region</th>
                           </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; //echo '<pre>'; print_r($publishings); echo '</pre>'; ?>
                         @foreach($publishings as $publishing)
                          <?php
                          /*if($publishing->status_name === 'pending in'){
                             $indexed_tat = App\Helpers\stopTat($publishing->rfi_start_date,$publishing->indexing_tat);
                          }else{
                            $indexed_tat = App\Helpers\timeRemaining($publishing->indexing_tat);
                          }
                           
                          if($indexed_tat['h']<1){
                              $time = '0 hrs';
                          }else{
                              $time = $indexed_tat['h'].' hrs '.$indexed_tat['m'].' min';
                          } */
                          if($publishing->aq_status_name){
                            $status = strtoupper($publishing->aq_status_name);
                          }elseif($publishing->pq_status_name){
                             $status = strtoupper($publishing->pq_status_name);
                          }else{
                             $status = 'TO BE STARTED';
                          }
                          ?>
                           <tr>
                             <td style="width:1%">{{$i++}}</td>
                             <td>{{$publishing->request_no}} </td> 
                             <td>{{$publishing->publish_by_name}}</td>
                             <td>{{$status}}</td>  
                             <td>{{$publishing->auditor_name}}</td>
                             <td>{{$publishing->request_type}}</td>                    
                             <td>{{$publishing->total_lane}}</td>                    
                             <td>{{$publishing->no_of_inlands}}</td>  
                             <td>{{$publishing->region_name}}</td>
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
      //$(".table").DataTable();
      var table = $('.table').DataTable( {
                      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                      responsive:true,
                      dom: 'lBfrtip',                  
                      buttons: ['excel'],
                      
                    });
    $('.buttons-excel').addClass('btn btn-info').removeClass('buttons-excel dt-button buttons-html5').prepend('<i class="fa fa-file-excel-o"></i>&nbsp');
    $('#example_length').addClass('col-md-2');

      /*$('a.popup').on('click', function(){
        var href=$(this).attr('href');
        //alert(href);
        var newWin = window.open(href, 'User Process Form','left=200,top=80,width=1100,height=700,toolbar=1,resizable=0');
        //newWin.document.close();
        if (window.focus) {newWin.focus()}
        return false;
      });*/
   });
      
   
 </script>
@endsection

           