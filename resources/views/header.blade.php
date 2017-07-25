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

<div class="row">
    		<div class="col-md-8">
    			<div class="row row-top-menu">
    				<div class="col-md-2 col-top-logo">	
    					<img src="{{URL::asset('img/logo-bks.png')}}" class="logo">
    				</div>    				
                    <div class="col-md-2 col-top-menu1">
                        <div class="dropdown">
                            <a class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Transaction
                                    <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <li>
                                     <a href="/transaction">Create Transaction</a>
                                </li>                                                                  
                                @if ($req->session()->get("role")!="staff")
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="/collect/list">Gabungan</a>
                                    </li>  
                                @endif
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="/transaction/taken">Customer Taken</a>
                                </li>                                                                
                            </ul>
                        </div>                      
                    </div>
                    
                    @if ( ($req->session()->get("role")=="administrator") || ($req->session()->get("role")=="admin") )   
                        <div class="col-md-2 col-top-menu1">
                            <div class="dropdown">
                                <a class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Laporan
                                        <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li>
                                         <a href="/report/biaya">Laporan Pelanggan</a>
                                    </li>  
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="/report/pengiriman">Laporan Pengiriman</a>
                                    </li>                                                          
                                </ul>
                            </div>                      
                        </div>
                    @endif

                    @if ( ($req->session()->get("role")=="administrator") || ($req->session()->get("role")=="admin") ) 
                        <div class="col-md-2 col-top-menu1">
                            <div class="dropdown">
                                <a class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Master Data
                                        <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">

                                    <li>
                                        <a href="/customer">Customer</a>
                                    </li>
                                    @if ($req->session()->get("role")=="administrator")
                                        <li role="separator" class="divider"></li>
                                        <li>
                                             <a href="/user/list">User</a>
                                        </li>  
                                    @endif
                                    <li role="separator" class="divider"></li>
                                    <li>
                                         <a href="/cities/list">City</a>
                                    </li>  
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="/agent/list">Perwakilan / zona</a>
                                    </li>
                                    <li role="separator" class="divider"></li>

                                    <li>
                                        <a href="/employ/list">Karyawan</a>
                                    </li>                                    
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="/price/list">Harga</a>
                                    </li>     
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="/tpl/list">3pl</a>
                                    </li>                                                          
                                </ul>
                            </div>                      
                        </div>   
                    @endif               
    			</div>
    		</div>
    	<div class="col-md-4">
    	<div class="row">
    		<div class="col-md-11 col-top-menu-user">
    			<div class="dropdown">
					<button class="btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						    {{\Auth::user()->first_name}} {{\Auth::user()->lasts_name}}
						    <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
 					    <!-- <li><a href="/user/list">User</a></li>	
                        <li role="separator" class="divider"></li>
                        <li><a href="/role/list">Role</a></li>  					    
					    <li role="separator" class="divider"></li> -->
				        <li><a href="/user/logout">logout</a></li>
		  		    </ul>
				</div>    					
    		</div>
    		<div class="col-md-1 col-top-menu-user">
    			<img src="{{URL::asset('img/user.png')}}" class="user" />
        	</div>
    	</div>
 	</div>
</div>

<div class="row">
        <div class="col-md-12">
            <div class="status-barang">{{ucwords(str_replace("_"," ",$type))}}</strong></div>
        </div>          
    </div>