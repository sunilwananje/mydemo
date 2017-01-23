@extends('layouts.default')
 @section('title')
    Add Menu
  @stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                   <h3 class="box-title"> Create Menu </h3>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('menu.store') }}" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group{{ $errors->has('menu_name') ? ' has-error' : '' }}">
                            <label for="menu_name" class="col-md-4 control-label">Menu Name</label>

                            <div class="col-md-6">
                                <input id="menu_name" type="text" class="form-control" name="menu_name" value="{{ old('menu_name') }}" style="text-transform: capitalize;">

                                @if ($errors->has('menu_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menu_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                         <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                            <label for="parent_id" class="col-md-4 control-label">Parent Menu</label>
                            <div class="col-md-6">
                                <select name="parent_id" class="form-control">
                                  <option value="">Select</option>
                                  <option value="0">Parent</option>
                                  @foreach(App\Model\Menu::all() as $menu)
                                    <option value="{{$menu->id}}">{{$menu->menu_name}}</option>
                                  @endforeach  
                                </select>
                                @if ($errors->has('parent_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parent_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group {{ $errors->has('permission_id') ? ' has-error' : '' }}">
                            <label for="permission_id" class="col-md-4 control-label">Permissions</label>
                            <div class="col-md-6">
                                <select name="permission_id" class="form-control">
                                  <option value="">Select</option>
                                  @foreach($permissionData as $permission)
                                    <option value="{{$permission->id}}">{{$permission->display_name}}</option>
                                  @endforeach  
                                </select>
                                @if ($errors->has('permission_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('permission_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-floppy-o"></i> Save
                        </button>
                        <a href="{{ route('menu.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
