@extends('layouts.default')
@section('title')
    Edit TAT
  @stop
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                   <h3 class="box-title"> Edit Mode Type </h3>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('tat.update',$recId->id) }}">
               
                    {{ csrf_field() }}

                    {{ method_field('PATCH') }}
                    <div class="box-body">
                       <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Priority Type</label>

                            <div class="col-md-6">
                                <select id="priority" type="text" class="form-control" name="priority_id" >
                                    <option>Please Select Priority Type</option>
                                 @foreach(\App\Model\PriorityType::all() as $val)   
                                    <option value="{{ $val->id }}" @if($recId->priority_id === $val->id) selected @endif> {{$val->name}} </option>
                                 @endforeach
                                </select>

                                @if ($errors->has('priority'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('priority') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('tat_time') ? ' has-error' : '' }}">
                            <label for="tat_time" class="col-md-4 control-label">TAT Time</label>

                            <div class="col-md-6">
                                <input id="tat_time" type="text" class="form-control" name="tat_time" placeholder="for eg. 24" value="{{$recId->tat_time or old('tat_time')}}">In Hours

                                @if ($errors->has('tat_time'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tat_time') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-edit"></i> Edit
                        </button>
                        <a href="{{ route('tat.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
