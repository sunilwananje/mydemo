@extends('layouts.default')

  @section('title')
     Home Page
  @stop

  @section('content')
      
      <div class="templatemo-content-wrapper">
        <div class="container">

            <ol class="breadcrumb">
                <li><a href="{{ url("/") }}"><font color="green"><i class="fa fa-dashboard"></i>Home</font></a></li>
                <li class="active">Vehicle Details</li>
            </ol>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                  
                </div>
            </div>
        </div>
    </div>

  @stop