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
		@if (count($errors))     
			<div class="row">				
				<div class="col-md-12 alert alert-danger">		
				    <ul>
				        @foreach($errors->all() as $error) 		            				            
				            <li>{{str_replace("name","Nama toko",$error)}}</li>
				        @endforeach 
				    </ul>
			    </div>
		    </div>
		@endif 
		<div class="row">	
			<form action="/user/list" method="get">				
				<div class="col-md-4">
					Email<br/>
					<input type="text" name="email" class="form-control" value="{{isset($filter["email"]) ? $filter["email"] : ""}}">
				</div>				
				<div class="col-md-8">
					<br/>
					<input type="submit" value="find" class="btn">
				</div>
			</form>
		</div>
		<br/>
		<div class="row">	
			<div class="col-md-12">
			<a href="/user/add">Create</a>
			</div>
		</div>
		<br/>
		<div class="row">	
			<div class="col-md-12">
				<table class="table">
					<?php 
						$str_parameter = "";
						if (isset($order_by)){
							if ($order_by=="asc"){
								$str_parameter = "&order_by=desc";
							}
							else if ($order_by=="desc"){
								$str_parameter = "&order_by=asc";
							}	
						}
					?>
					<thead>
						<th>Firstname</th>
			    		<th>Lastname</th>
			    		<th>email</th>	
			    		<th>role</th>
			    		<th>agent</th>			    																			
						<th>Action</th>
					</thead>
					<tbody>
						@foreach ($users as $key => $value)
							<tr>
								<td>{{$value->first_name}}</td>
								<td>{{$value->last_name}}</td>
								<td>{{$value->email}}</td>
								<td>{{$value->role}}</td>
								<td>
									@if (isset($value->agent_name))
										{{$value->agent_name}}, {{$value->city_name}}
									@endif
								</td>
								<td>
									<a href="/user/edit/{{$value->id}}">
										<span class="edit"> 
					    					<span class="glyphicon glyphicon-pencil"></span>
					    				</span>
				    				</a> | 
				    				<a href="/user/delete/{{$value->id}}" class="confirmation">
					    				<span class="delete">
				    						<span class="glyphicon glyphicon-remove"></span>
				    					</span> 
			    					</a>
								</td>
							</tr>																							
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	 </div>	    	
</div>
</body>
</html>