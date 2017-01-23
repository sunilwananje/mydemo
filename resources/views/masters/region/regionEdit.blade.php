@extends('layouts.default')
  @section('title')
    Edit Region
  @stop
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Edit Region</h3>
                </div>
                
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('region.update',$recId->id) }}">
               
                    {{ csrf_field() }}

                    {{ method_field('PATCH') }}
                   <div class="box-body">
                    <div class="form-group{{ $errors->has('region_name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-4 control-label">Region Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" value="{{ $recId->name or old('name') }}">

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('region_volume') ? ' has-error' : '' }}">
                        <label for="region_volume" class="col-md-4 control-label">Average volume</label>

                        <div class="col-md-6">
                            <input id="region_volume" type="text" class="form-control" name="region_volume" value="{{ $recId->region_volume }}">

                            @if ($errors->has('region_volume'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('region_volume') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('region_abbr') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">Region Abbreviation</label>
                        <div class="col-md-6">
                            <textarea id="region_abbr" class="form-control" name="region_abbr">{{ $recId->region_abbr or old('region_abbr') }}</textarea> 
                            @if ($errors->has('region_abbr'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('region_abbr') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                  </div>
                   <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-pencil"></i> Update
                        </button>
                        <a href="{{ route('region.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
