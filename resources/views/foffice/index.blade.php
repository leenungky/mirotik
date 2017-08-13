<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     @include('head')
     <style type="text/css" media="print">
     	   @media print {
			    @page { margin: 0px 6px; }
  				body  { margin: 0px 6px; }   					  
			}
     </style>
</head>
<body >
    <?php use App\Http\Helpers\Helpdesk; ?>
 
 <div id="contents">
    <div class="container container-fluid">            	
		@include('header')		
		<br/>		
		@if ($role=="administrator")
			<div class="row">	
				<div class="col-md-12">
				<a href="/customer/add">Create</a>
				</div>
			</div>
			<br/>
		@endif
		 @if(Session::has('message'))
            <div class="row">
                <div class="col-md-12 alert alert-warning">      
                    <ul>
                        <li>{!! Session::get('message') !!}</li>                      
                    </ul>
                </div>
            </div>
            <br/>
        @endif               
		
		<div class="row">	
			<div class="col-md-12">
				<table class="table">					
					<thead>
						<th>No</th>
						<th>Username</th>			    																				
						<th>Password</th>			    																				
						<th>Action</th>
					</thead>
					<tbody>
						@if (isset($usermkr))
						@foreach ($usermkr as $key => $value)
							<tr>
								<td>{{($key+1)}}</td>
								<td>{{$value["username"]}}</td>								
								<td>{{$value["password"]}}</td>								
								<td>
									@if ($role=="administrator")
										<a href="/customer/edit/{{$value[".id"]}}">
											<span class="edit"> 
						    					<span class="glyphicon glyphicon-pencil"></span>
						    				</span>
					    				</a> | 
					    				<a href="/customer/delete/{{$value[".id"]}}" class="confirmation">
						    				<span class="delete">
					    						<span class="glyphicon glyphicon-remove"></span>
					    					</span> 
				    					</a>
				    					| 			    					
			    					@endif
			    					<a href="javascript:void(0)" class="print" val="{{$value[".id"]}}">
			    						 <span class="glyphicon glyphicon-print"></span> 
			    					</a>
								</td>
							</tr>																							
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>
	 </div>	    	
</div>
</body>
</html>