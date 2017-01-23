<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>F2F SQ Creation | @yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/assets/dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('/assets/dist/css/skins/_all-skins.min.css') }}">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ asset('/assets/bootstrap/css/bootstrap.min.css') }}">
  <style>
    #loaderDiv {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1100;
    background-color: white;
    opacity: .6;
}
.ajax-loader {
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -32px;
    /* -1 * image width / 2 */
    margin-top: -32px;
    /* -1 * image height / 2 */
    display: block;
}
  </style>
  @yield('styles')
  
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loaderDiv" style="display: none">
  <img src="{{ asset ('/assets/dist/img/animated-overlay.gif') }}" class="ajax-loader"/>
</div>
<div class="wrapper">

  @include('includes.header')
  
    <!-- sidebar -->
        @include('includes.sidebar')
    <!-- /sidebar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @yield('page-header')

    <!-- Main content -->
    <section class="content">
       @yield('content')
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- footer content -->
    @include('includes.footer')
  <!-- /footer content -->

</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="{{ asset('/assets/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('/assets/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('/assets/dist/js/app.min.js') }}"></script>
<!-- SlimScroll 1.3.0 -->
<script src="{{ asset('/assets/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- ChartJS 1.0.1 -->

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('/assets/dist/js/demo.js') }}"></script>
<!-- customize js -->
<script src="{{ asset('/assets/dist/js/custom.js') }}"></script>


@yield('script')
</body>
</html>
