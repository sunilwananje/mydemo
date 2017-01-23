@extends('layouts.default')
  @section('title')
    Add RFI Type
  @stop
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                   <h3 class="box-title"> Create RFI Type </h3>
                </div>
                
                @if(session()->has('message'))
                <div class="alert alert-success" role="alert">
                    {{session()->get('message')}}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger" role="alert">
                    {{session()->get('error')}}
                </div>
                @endif
                <form class="form-horizontal" role="form" method="POST" action="{{ route('rfi.store') }}">
                    {{ csrf_field() }}
                     <div class="box-body">
                        <div class="form-group{{ $errors->has('rfi_type_name') ? ' has-error' : '' }}">
                            <label for="rfi_type_name" class="col-md-4 control-label">RFI Name</label>

                            <div class="col-md-6">
                                <input id="rfi_type_name" type="text" class="form-control" name="rfi_type_name" value="{{ old('rfi_type_name') }}">

                                @if ($errors->has('rfi_type_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('rfi_type_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-floppy-o"></i> Save
                        </button>
                        <a href="{{ route('rfi.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
