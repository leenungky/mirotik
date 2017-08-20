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
				<table class="table" cellspacing="0" cellpadding="0">					
						<thead>
						<tr>
						<th class="first">No</th>
						<th>Name</th>			    				
						<th>Room</th>						
						<th>Checkin</th>
						<th>Checkout</th>						
						<th>created_by</th>
						<th>created_at</th>
						<th>update_by</th>
						<th>update_at</th>
						<th>delete_by</th>
						<th>delete_at</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($report as $key => $value)
							<tr>
								<td class="first">{{$key+1}}</td>
								<td>{{$value->name}}</td>								
								<td>{{$value->room}}</td>								
								<td>{{$value->checkin}}</td>
								<td>{{$value->checkout}}</td>
								<td>{{$value->vcreate}}</td>
								<td>{{$value->created_at}}</td>
								<td>{{$value->vupdate}}</td>
								<td>{{$value->updated_at}}</td>
								<td>
									@if ($value->deleted_by==-1)
										System
									@else
										{{$value->vdelete}}
									@endif									
								</td>
								<td>{{$value->deleted_at}}</td>								
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

