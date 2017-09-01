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
			@media print {
				.txt-attr {
			    	color: orange !important;			    
			}
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
				            <li>{{$error}}</li>
				        @endforeach 
				    </ul>
			    </div>
		    </div>
		@endif 		
		<div class="row">				
			<div class="col-md-12">
				<a href="/report/pdf" class="btn btn-primary">Export to PDF</a>
			</div>
		</div>
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
						<th>No</th>
						<th>Name</th>			    				
						<th>Room</th>						
						<th>Action</th>
						<th>Action By</th>	
						<th>Action Date</th>						
					</thead>
					<tbody>
						@foreach ($report as $key => $value)
							<tr>
								<td>{{$key+1}}</td>
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
		 <div class="row">
            <div class="col-md-12">   
            <?php 
                if (isset($input)){
                    $report->appends($input);
                }
            ?>
            {!! $report->render() !!}
            </div>
	 </div>	    	
	 </div>	    	
</div>

</body>
</html>

