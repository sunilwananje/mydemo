@extends('layouts.default')
  @section('title')
    Edit Error Category
  @stop
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="box box-primary">
                  <h3 class="box-title"> Edit  Error Category </h3>
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
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('errorCat.update',$recId->id) }}">
                   
                        {{ csrf_field() }}

                        {{ method_field('PATCH') }}
                      <div class="box-body">
                        <div class="form-group{{ $errors->has('errorCat_name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label"> Error Category Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $recId->name or old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                      </div>
                       

                        
                        <div class="box-footer">
                           <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-pencil"></i> Update
                            </button>
                            <a href="{{ route('errorCat.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection
