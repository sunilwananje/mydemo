@extends('layouts.default')
   @section('title')
    Pricing Database
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
                  <h3 class="box-title">Pricing Database</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered table-striped table-hover" style="font-size:small">
                         
                          <thead style="background: #3c8dbc;color: white;">
                           <tr>
                             <td></td> 
                             <td></td> 
                             <td><input type="text" name="pol_search" id="pol_search" placeholder="Search POL Port" class="form-control"></td>
                             <td></td> 
                             <td ><input type="text" name="pod_search" id="pod_search" placeholder="Search POD Port" class="form-control"></td> 
                             <td></td> 
                             <td></td> 
                             <td></td> 
                           </tr>
                            <tr >
                             <th>Serial No.</th>     
                             <th>SQ No</th>
                             <th>POL Port</th>
                             <th>POL region</th>
                             <th>POD Port</th> 
                             <th>POD region</th>
                             <th>Pricer Name</th>
                             <th>Updated By</th>
                            </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach($pricerData as $pricer)
                           <tr class="">
                             <td style="width:1%">{{$i++}}</td>
                             <td>{{$pricer->sq_no}}</td> 
                             <td>{{$pricer->pol_port}}</td>
                             <td>{{$pricer->pol_region}}</td>
                             <td>{{$pricer->pod_port}}</td>
                             <td>{{$pricer->pod_region}}</td>
                             <td>{{$pricer->pricer_name}}</td>
                             <td>{{$pricer->updated_user}}</td>
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

<!-- modal end -->
@section('script')
 <script src="{{ asset ('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
 <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
 <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
 <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
 <script>
   $(document).ready(function () {
      $(".table").DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive:true,
        dom: 'lBfrtip',                  
        buttons: ['excel'],
      });

      $('.buttons-excel').addClass('btn btn-info').removeClass('buttons-excel dt-button buttons-html5').prepend('<i class="fa fa-file-excel-o"></i>&nbsp');
      $('#example_length').addClass('col-md-2');

      $( '#pol_search').on( 'keyup change', function () {
            if ( table.column(2).search() !== this.value ) {
                 table.column(2).search( this.value ).draw();
            }
        } );
      $( '#pod_search').on( 'keyup change', function () {
            if ( table.column(4).search() !== this.value ) {
                 table.column(4).search( this.value ).draw();
            }
        } );
  });
 </script>
@endsection

           