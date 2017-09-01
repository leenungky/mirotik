<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>     
     <style type="text/css">
     	table tbody tr td{
     		border-bottom: 1px solid black; 
     		border-right: 1px solid black;
     		padding:1px;
     		margin: 0px;     		
     	}

     	.first{
     		border-left: 1px solid black;
     	}

     	table thead th{
     		border-bottom: 1px solid black; 
     		border-right: 1px solid black;
     		border-top: 1px solid black;
     		padding:1px;
     		margin: 0px;     		
     	}
     </style>
</head>
<body >
    <?php use App\Http\Helpers\Helpdesk; ?>
 
 <div id="contents">
    <div class="container container-fluid">            			
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
							<tr>
						<th class="first">No</th>
						<th>Name</th>			    				
						<th>Room</th>						
						<th>Action</th>
						<th>Action By</th>	
						<th>Action Date</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($report as $key => $value)
							<tr>
								<td class="first">{{$key+1}}</td>
								<td>{{$value->name}}</td>
								<td>{{$value->room}}</td>								
								<td>{{$value->action}}</td>															
								<td>{{$value->username}}</td>	
								<td>{{$value->created_at}}</td>
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

