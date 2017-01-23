<header class="main-header">

    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>F</b>2F</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{{SITE_TITLE}}</b></span>
    </a>
   
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- <span id="date_time_clock"></span> -->
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle Navigation</span>
      </a>
      
      <!-- Navbar Right Menu -->
      
      <div class="navbar-custom-menu">
       <!-- <div id="date_time_clock"></div> -->
        <ul class="nav navbar-nav">
          <!-- Notifications: style can be found in dropdown.less -->
          
          <!-- Tasks: style can be found in dropdown.less -->
        <li id="date_time_clock" class="hidden-xs" style="padding: 6px 500px 0 0;"></li>
          <!-- User Account: style can be found in dropdown.less -->
          @if(Session::has('ldap_username'))
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{ asset('/assets/dist/img/default-profile.png') }}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{Session::get('ldap_name')}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{ asset('/assets/dist/img/default-profile.png') }}" class="img-circle" alt="User Image">

                <p>
                  {{Session::get('ldap_name')}}
                  <!-- <small>Member since Nov. 2012</small> -->
                </p>
              </li>
              <!-- Menu Body -->
              <!-- <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
              </li> -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <!-- <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div> -->
                <div class="pull-right">
                  <a href="{{route('auth.logout')}}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          @endif
          <!-- Control Sidebar Toggle Button -->
         <!--  <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>

    </nav>
  </header>