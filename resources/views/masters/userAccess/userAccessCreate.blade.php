@extends('layouts.default')
 @section('title')
    Add User Access
  @stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                   <h3 class="box-title"> Create User Access </h3>
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
                <form class="form-horizontal" role="form" method="POST" action="{{ route('userAccess.store') }}" autocomplete="off" id="userForm">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">   Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" style="text-transform: capitalize;">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">Window Username</label>
                            <div class="col-md-6">
                            <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}">
                                
                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
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
                                    <option value="{{$office->id}}">{{$office->office_name}}</option>
                                  @endforeach  
                                </select>
                                @if ($errors->has('office_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('office_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                         <div class="form-group {{ $errors->has('role_id') ? ' has-error' : '' }}">
                            <label for="role_id" class="col-md-4 control-label">Role</label>
                            <div class="col-md-6">
                                <select name="role_id" class="form-control">
                                  <option value="">Select</option>
                                  @foreach(App\Model\Role::all() as $role)
                                    <option value="{{$role->id}}" {{(($role->id==old('role_id'))?'selected':'')}}>{{ucfirst($role->name)}}</option>
                                  @endforeach  
                                </select>
                                  @if ($errors->has('role_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('role_id') }}</strong>
                                    </span>
                                  @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="role_id" class="col-md-4 control-label">Permissions</label>
                            <div class="col-md-6">
                             @foreach($menuData as $permission)
                               <div class="panel-group">
                                   <div class="panel panel-default collapsed-panel">
                                      <div class="panel-heading">
                                        <h4 class="panel-title">
                                          <input type="checkbox" name="parent_menu[]" value="{{$permission->permission_id}}" class="minimal parent" id="{{$permission->id}}">
                                          <a data-toggle="collapse" href="#collapse{{$permission->id}}">
                                          <label>{{$permission->menu_name}}</label>
                                           <div class="panel-tools pull-right">
                                            <i class="more-less fa fa-plus"></i>
                                           </div>
                                          </a>
                                        </h4>

                                      </div>
                                      <div id="collapse{{$permission->id}}" class="panel-collapse collapse">
                                         <ul style="list-style: none;">
                                           @foreach($permission->childMenu as $menu)
                                           <li>
                                              <input type="checkbox" name="users_permission[]" value="{{$menu->permission_id}}" id="{{$menu->id}}" class="per-{{$menu->parent_id}} submenu"><label for="{{$menu->id}}">{{$menu->menu_name}}</label>
                                              @foreach($menu->childMenu as $submenu)
                                                <input type="checkbox" name="users_permission[]" value="{{$submenu->permission_id}}" id="{{$submenu->id}}" class="per-{{$menu->parent_id}} sub-{{$submenu->parent_id}}" style="display:none;">
                                              @endforeach  
                                           </li>

                                           @endforeach  
                                        </ul>
                                     </div>
                                    </div>
                                  </div>
                               @endforeach
                                
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-floppy-o"></i> Save
                        </button>
                        <a href="{{ route('userAccess.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function(){
    $('#userForm').submit(function() {
        $('#loaderDiv').css('display', 'block');
        return true;
    });
     
  });
  function toggleIcon(e) {
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('fa fa-plus fa fa-minus');
  }
  $('.panel-group').on('hidden.bs.collapse', toggleIcon);
  $('.panel-group').on('shown.bs.collapse', toggleIcon);
</script>
@endsection
