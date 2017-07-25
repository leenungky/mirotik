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
		<div class="row">	
			<div class="col-md-12">
			<a href="/agent/add">Create</a>
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
						<th>Name</th>
			    		<th>City name</th>		
			    		<th>Phone</th>
			    		<th>Address</th>			    		
						<th>Action</th>
					</thead>
					<tbody>
						@foreach ($agent as $key => $value)
							<tr>
								<td>{{$value->name}}</td>
								<td>{{$value->kota}}</td>
								<td>{{$value->phone}}</td>
								<td>{{$value->address}}</td>
								<td>
									<a href="/agent/edit/{{$value->id}}">
										<span class="edit"> 
					    					<span class="glyphicon glyphicon-pencil"></span>
					    				</span>
				    				</a> | 
				    				<a href="/agent/delete/{{$value->id}}" class="confirmation">
					    				<span class="delete">
				    						<span class="glyphicon glyphicon-remove"></span>
				    				</sp
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