<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset('/assets/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{Session::get('ldap_name')}}</p>
          <!-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a> -->
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <?php 
        $permissionArray = Session::get('permissions'); 
        $masterArray = array('userAccess.index','office.index','region.index','containerType.index','errorCat.index','errorType.index','priorityType.index','pricingArea.index','mode.index','holiday.index','tat.index','status.index','rfi.index');
        $followupArray = array('queue.followup','queue.pricer','queue.partnercode');
        $reportArray = array('report.daily','report.weekly','report.monthly');
        //$result = !empty(array_intersect($permissionArray, $masterArray));
      ?>
      <ul class="sidebar-menu">
        <li class="no-child"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        @if(!empty(array_intersect($permissionArray, $masterArray)))
        <li class="treeview">
          <a href="#">
            <i class="fa fa-sitemap"></i> <span>Masters</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          @if(in_array('userAccess.index',$permissionArray))
            <li><a href="{{route('userAccess.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Users</a></li>
          @endif
          @if(in_array('office.index',$permissionArray))
            <li><a href="{{route('office.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Office</a></li>
          @endif
          @if(in_array('region.index',$permissionArray))
            <li><a href="{{route('region.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Region</a></li>
          @endif
          @if(in_array('office.index',$permissionArray))
            <li><a href="{{route('requestType.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Request Type</a></li>
          @endif
          @if(in_array('containerType.index',$permissionArray))
            <li><a href="{{route('containerType.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Container Type</a></li>
          @endif
          @if(in_array('errorCat.index',$permissionArray))
            <li><a href="{{route('errorCat.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Error Category</a></li>
          @endif
          @if(in_array('errorType.index',$permissionArray))
            <li><a href="{{route('errorType.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Error Type</a></li>
          @endif
          @if(in_array('priorityType.index',$permissionArray))
            <li><a href="{{route('priorityType.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Priority Type</a></li>
          @endif
          
          @if(in_array('mode.index',$permissionArray))
            <li><a href="{{route('mode.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Modes</a></li>
          @endif
          @if(in_array('pricingArea.index',$permissionArray))
            <li><a href="{{route('pricingArea.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Pricing Area</a></li>
          @endif
          @if(in_array('holiday.index',$permissionArray))
            <li><a href="{{route('holiday.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Holidays</a></li>
          @endif
          @if(in_array('tat.index',$permissionArray))
            <li><a href="{{route('tat.index')}}" class="child-item"><i class="fa fa-circle-o"></i> TAT</a></li>
          @endif
          @if(in_array('status.index',$permissionArray))
            <li><a href="{{route('status.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Status</a></li>
          @endif
          @if(in_array('rfi.index',$permissionArray))
            <li><a href="{{route('rfi.index')}}" class="child-item"><i class="fa fa-circle-o"></i> RFI Type</a></li>
          @endif

          @if(in_array('reminder.index',$permissionArray))
            <li><a href="{{route('reminder.index')}}" class="child-item"><i class="fa fa-circle-o"></i> Reminder Mail Setting</a></li>
          @endif
          
          </ul>
        </li>
       @endif
        @if(in_array('indexing.create',$permissionArray))
        <li class="no-child"><a href="{{ route('indexing.create') }}"><i class="fa fa-list-alt"></i> <span>Indexing</a></span></li>
        @endif

        @if(in_array('publishing.index',$permissionArray))
        <li class="no-child"><a href="{{ route('publishing.index') }}"><i class="fa fa-list-alt"></i> <span>Publishing</span></a></li>
        @endif

        @if(in_array('admin.tracker.list',$permissionArray))
        <li class="no-child"><a href="{{ route('admin.tracker.list') }}"><i class="fa fa-th-list"></i> <span>Tracker List (All / Live Data)</span></a></li>
        @endif

        @if(in_array('auditing.index',$permissionArray))
        <li class="no-child"><a href="{{ route('auditing.index') }}"><i class="fa fa-file-text"></i> <span>Auditing Queue</span></a></li>
        @endif
        
        @if(in_array('queue.rfi',$permissionArray))
        <li class="no-child"><a href="{{ route('queue.rfi') }}"><i class="fa fa-retweet"></i> <span>RFI Queue</span></a></li>
        @endif

        @if(in_array('queue.completed',$permissionArray))
        <li class="no-child"><a href="{{ route('queue.completed') }}"><i class="fa fa-check-square"></i> <span>Completed Queue</span></a></li>
        @endif
        @if(!empty(array_intersect($permissionArray, $followupArray)))
        <li class="treeview">
          <a href="#">
            <i class="fa fa-random"></i> <span>Follow Up</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(in_array('queue.followup',$permissionArray))
            <li><a href="{{ route('queue.followup') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Follow Up Queue</span></a></li>
            @endif
            @if(in_array('queue.pricer',$permissionArray))
            <li><a href="{{ route('queue.pricer') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Pricer Queue</span></a></li>
            @endif
            @if(in_array('queue.partnercode',$permissionArray))
            <li><a href="{{ route('queue.partnercode') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Partnercode Queue</span></a></li>
            @endif
          </ul>
        </li>
       @endif
       @if(!empty(array_intersect($permissionArray, $reportArray)))
        <li class="treeview">
          <a href="#">
            <i class="fa fa-list"></i> <span>Reports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(in_array('report.daily',$permissionArray))
            <li><a href="{{ route('report.daily') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Daily Report</span></a></li>
            @endif
            @if(in_array('report.weekly',$permissionArray))
            <li><a href="{{ route('report.weekly') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Weekly Report</span></a></li>
            @endif
            @if(in_array('report.monthly',$permissionArray))
            <li><a href="{{ route('report.monthly') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Monthly Report</span></a></li>
            @endif
            @if(in_array('report.productivity',$permissionArray))
            <li><a href="{{ route('report.productivity') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Productivity Report</span></a></li>
            @endif
            @if(in_array('report.rfiLog',$permissionArray))
            <li><a href="{{ route('report.rfiLog') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>RFI Log Report</span></a></li>
            @endif
            @if(in_array('report.capa',$permissionArray))
            <li><a href="{{ route('report.capa') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>CAPA Report</span></a></li>
            @endif
            @if(in_array('report.errors',$permissionArray))
            <li><a href="{{ route('report.errors') }}" class="child-item"><i class="fa fa-circle-o"></i> <span>Error Report</span></a></li>
            @endif
          </ul>
        </li>
       @endif
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>