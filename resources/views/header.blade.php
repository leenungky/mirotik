 <script type="text/javascript">
    var base_url = "{{config('config.url')}}";
    $(document).ready(function(){
        $(document).idleTimeout({
            inactivity: 3000000, 
            noconfirm: 900000,      
            sessionAlive:900000,
            redirect_url :base_url + "/user/logout"
        });
    });
</script>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="/customer/list">MIKROTIK</a>
    </div>
    <div>
      <ul class="nav navbar-nav">        
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">IP
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/customer/list">Hostpot</a></li>                          
          </ul>
        </li>        
        @if ($role==config("config.supervisor"))   
        <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Master Data
              <span class="caret"></span></a>
              <ul class="dropdown-menu">                  
                    <li><a href="/user/list">User</a></li>                                              
                    <li role="separator" class="divider"></li>  
                  
                    <li><a href="/room/list">Room</a></li>      
                    <li role="separator" class="divider"></li>                    
                    <li><a href="/meetroom/list">Meeting Room</a></li>      
                    <!--<li role="separator" class="divider"></li>
                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Item 2<i class="glyphicon glyphicon-chevron-right"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="/payroll/list">Item 2 1</a></li>
                            <li role="separator" class="divider"></li>                                             
                        </ul>
                    </li>  -->  
              </ul>
            </li>        
          <li><a href="/report/list">Report</a></li>     
          @endif
    </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"> {{\Auth::user()->username}}<img src="{{URL::asset('img/user.png')}}" class="user" /><span  class="glyphicon glyphicon-log-in"></span></a>
            <ul class="dropdown-menu">
            <li><a href="/user/password">Change Password</a></li>                 
            <li role="separator" class="divider"></li>                    
            <li><a href="/user/logout">logout</a></li>             
        </ul>          
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="row">
        <div class="col-md-12">
            <div class="status-barang">{{ucwords(str_replace("_"," ",$type))}}</strong></div>
        </div>          
    </div> 