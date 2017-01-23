@extends('layouts.default')
   @section('title')
    Partner Code Database
  @stop
  @section('styles')
     <link rel="stylesheet" href="{{ asset ('/assets/plugins/datatables/dataTables.bootstrap.css') }}">
     <!-- <link rel="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
  @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Partner Code Database</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive"><!-- class="table table-striped table-bordered table-hover no-margin" -->
                        <table id="example" class="table table-bordered table-striped table-hover" style="font-size:small">
                          <thead style="background: #3c8dbc;color: white;">
                            <tr>
                             <th>Serial No.</th>     
                             <th>Shipper Name</th>
                             <th>Address</th>
                             <th>City</th>
                             <th>State</th> 
                             <th>Partner Code</th>
                            </tr>
                         </thead>
                         <tbody>
                         <?php $i=1; ?>
                         @foreach($partnerData as $partner)
                           <tr class="">
                             <td style="width:1%">{{$i++}}</td>
                             <td>{{$partner->shipper_name}}</td> 
                             <td>{{$partner->address}}</td>
                             <td>{{$partner->city}}</td>
                             <td>{{$partner->state}}</td>
                             <td>{{$partner->partner_code}}</td>
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

      $('.buttons-excel').addClass('btn btn-info').removeClass('buttons-excel dt-button buttons-html5').html('<i class="fa fa-file-excel-o"></i>&nbspExport to Excel');
      $('#example_length').addClass('col-md-7');
      $('#example_filter').addClass('col-md-1').css('margin-left','83px');
      $('.dt-buttons').addClass('pull-right');
  });
 </script>
@endsection

           