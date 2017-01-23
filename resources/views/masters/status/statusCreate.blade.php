@extends('layouts.default')
@section('title')
    Add Status
  @stop
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                   <h3 class="box-title"> Add Status </h3>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('status.store') }}">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group{{ $errors->has('status_name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Status Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="status_name" value="{{ old('status_name') }}">
                                
                                @if ($errors->has('status_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('status_type') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Status Type</label>

                            <div class="col-md-6">
                                <select class="form-control" name="status_type">
                                    <option value="" >Select</option>
                                    <option value="upf" {{ ((old('status_type')=='upf')?'selected':'')}}>UPF</option>
                                    <option value="apf" ((old('status_type')=='apf')?'selected':'')>APF</option>
                                </select>
                                
                                @if ($errors->has('status_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-floppy-o"></i> Save
                        </button>
                        <a href="{{ route('status.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
