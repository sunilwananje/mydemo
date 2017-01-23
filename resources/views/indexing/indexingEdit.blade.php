@extends('layouts.default')
 @section('title')
    Edit Indexing
  @stop
@section('content')
@section('styles')
  <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui.css') }}">
  <link rel="stylesheet" href="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.css') }}">
@endsection
<?php
/*$weeknumber = date("W");
$month = date("m");
$date = date("d");
$rquest_number = 'WK/'.$weeknumber.'/'.$date.'/'.$month.'/#';*/
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">

                <div class="box-header with-border">
                   <h3 class="box-title"> Edit Indexing </h3>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('indexing.update',$indexing->id) }}" id="indexingForm">
                    {{ csrf_field() }}{{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group{{ $errors->has('mail_received_time') ? ' has-error' : '' }}">
                            <label for="mail_received_time" class="col-md-3 control-label">Mail Received Time <span style="color:#FF0000">*</span></label>
                            <div class='input-group date col-md-6'>
                                <input type='text' class="form-control datetimepicker" name="mail_received_time" id="mail_received_time" data-url="{{url('requestNum')}}" value="{{ date('d-m-Y H:i ',strtotime($indexing->mail_received_time))}}" autocomplete="off" autofocus/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                              </div>
                             
                             
                             @if ($errors->has('mail_received_time'))
                                    <p class="help-block" style="margin-left:268px">
                                        <strong>{{ $errors->first('mail_received_time') }}</strong>
                                    </p>
                                @endif
                        </div>
                        <?php /*/ ?>
                          <div class='col-md-3'>
                            <button type="button" class="btn btn-success" id="request_no_btn">Get Request No.</button>
                          </div>
                          <?php /*/ ?>
                        <div class="form-group{{ $errors->has('request_no') ? ' has-error' : '' }}">
                            <label for="request_no" class="col-md-3 control-label">Request No. <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <input id="request_no" type="text" class="form-control" name="request_no" value="{{ $indexing->request_no or old('request_no')}}" disabled>

                                @if ($errors->has('request_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('request_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                       
                         
                        <div class="form-group{{ $errors->has('customer_name') ? ' has-error' : '' }}">
                            <label for="customer_name" class="col-md-3 control-label">Customer Name <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control capital-letter" name="customer_name" value="{{ $indexing->customer_name }}" style="text-transform: uppercase;">
                                @if ($errors->has('customer_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('customer_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('priority_id') ? ' has-error' : '' }}">
                            <label for="priority_id" class="col-md-3 control-label">Priority <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <select name="priority_id" id="priority_id" class="form-control">
                                  @foreach(App\Model\PriorityType::all() as $priority)
                                    <option value="{{$priority->id}}" {{(($priority->id==$indexing->priority_id)?'selected':'')}}>{{$priority->name}}</option>
                                  @endforeach
                                    <!-- <option value="Standard">Standard</option>
                                    <option value="Urgent">Urgent</option> -->
                                </select>

                                @if ($errors->has('priority_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('priority_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('region_id') ? ' has-error' : '' }}">
                            <label for="region_id" class="col-md-3 control-label">Region <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <select name="region_id" id="region_id" class="form-control">
                                   <option value="">Select</option>
                                  @foreach(App\Model\Region::all() as $region)
                                    <option value="{{$region->id}}" {{(($region->id==$indexing->region_id)?'selected':'')}}>{{$region->name}}</option>
                                  @endforeach
                                </select>

                                @if ($errors->has('region_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('region_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('request_type_id') ? ' has-error' : '' }}">
                            <label for="request_type_id" class="col-md-3 control-label">Request Type <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <select name="request_type_id" id="request_type_id" class="form-control">
                                    <option value="">Select</option>
                                  @foreach(App\Model\RequestType::all() as $request_type)
                                    <option value="{{$request_type->id}}" {{(($request_type->id==$indexing->request_type_id)?'selected':'')}}>{{$request_type->name}}</option>
                                  @endforeach
                                 </select>
                                @if ($errors->has('request_type_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('request_type_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('office_id') ? ' has-error' : '' }}">
                            <label for="office" class="col-md-3 control-label">Office <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <select name="office_id" id="office_id" class="form-control">
                                    <option value="">Select</option>
                                  @foreach(App\Model\Office::all() as $office)
                                    <option value="{{$office->id}}" {{(($office->id==$indexing->office_id)?'selected':'')}}>{{$office->office_name}}</option>
                                  @endforeach
                                </select>

                                @if ($errors->has('office_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('office_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                         <div class="form-group{{ $errors->has('indexed_by') ? ' has-error' : '' }}">
                            <label for="indexed_by" class="col-md-3 control-label">Indexed By <span style="color:#FF0000">*</span></label>
                            <div class="col-md-6">
                                <select name="indexed_by" id="indexed_by" class="form-control">
                                    <option value="">Select</option>
                                  @foreach(App\Model\User::all() as $user)
                                    <option value="{{$user->id}}" {{(($user->id==$indexing->indexed_by)?'selected':'')}}>{{$user->name}}</option>
                                  @endforeach
                                </select>
                                @if ($errors->has('indexed_by'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('indexed_by') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="office" class="col-md-3 control-label">Comments</label>
                            <div class="col-md-6">
                                <textarea name="comments" class="form-control">{{$indexing->comments or old('comments')}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-floppy-o"></i> Save
                        </button>
                        <a href="{{ route('indexing.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/jQueryUI/jquery-ui-timepicker-addon.js') }}"></script>
    <script src="{{ asset('/assets/dist/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('/assets/dist/js/validation.js') }}"></script>
@endsection