@extends('layouts.default')

  @section('title')
     Home Page
  @stop

  @section('page-header')
   <section class="content-header">
      <h1>
        Dashboard
        <!-- <small>Version 2.0</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
  @stop

  @section('content')
  <?php //print_r(Session::get('permissions')) ?>
    @if(session()->has('permission_error'))
    <div class="alert alert-warning" role="alert">
        <i class="fa fa-ban"></i> {{session()->get('permission_error')}}
    </div>
    @endif
     
      <!-- /.row -->
  @stop