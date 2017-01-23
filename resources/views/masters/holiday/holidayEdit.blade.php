@extends('layouts.default')
  @section('title')
    Edit Holiday
  @stop
  @section('styles')
    <link rel="stylesheet" href="{{ asset('/assets/plugins/datepicker/datepicker3.css') }}">
  @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
           <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Edit Holiday</h3>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('holiday.update',$holidayData->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Holiday Name</label>
                             <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $holidayData->name or old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('holiday_date') ? ' has-error' : '' }}">
                            <label for="holiday_date" class="col-md-4 control-label">Holiday Date</label>
                            <div class="col-md-6">
                               <input id="holiday_date" type="text" class="form-control datepicker" name="holiday_date" value="{{ date('d-m-Y',strtotime($holidayData->holiday_date))}}">
                                @if ($errors->has('holiday_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('holiday_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group {{ $errors->has('office_id') ? ' has-error' : '' }}">
                            <label for="office_id" class="col-md-4 control-label">Office</label>
                            <div class="col-md-6">
                                <select name="office_id" class="form-control">
                                  <option value="">Select</option> 
                                  @foreach(App\Model\Office::all() as $office)
                                    @if($office->id==$holidayData->office_id)
                                      <option value="{{$office->id}}" selected>{{$office->office_name}}</option>
                                    @else
                                      <option value="{{$office->id}}">{{$office->office_name}}</option>
                                    @endif
                                  @endforeach 
                                </select>
                                @if ($errors->has('office_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('office_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                     <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-pencil"></i> Update
                        </button>
                        <a href="{{ route('holiday.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset ('/assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script>
    $(document).ready(function(){
        $('.datepicker').datepicker({
            format:'dd-mm-yyyy',
         });
    });
</script>
@endsection