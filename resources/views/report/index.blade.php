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
			<form action="/report/list" method="get">
				<div class="col-md-2">
					FROM <br/>
					<input type="text" name="from" class="form-control datepicker" value="{{isset($filter["from"]) ? $filter["from"] : ""}}">	
				</div>
				<div class="col-md-2">
					TO <br/>
					<input type="text" name="to" class="form-control datepicker" value="{{isset($filter["to"]) ? $filter["to"] : ""}}">	
				</div>
				<div class="col-md-2">
					<br/>
					<input type="submit" value="find" class="btn btn-primary">
				</div>
			</form>
			<div class="col-md-6">
				<br/>
				<a href="/report/pdf?{{$parameter}}" class="btn btn-primary" style="float: right;">Export to PDF</a>
			</div>
		</div>
		<br/>
		<div class="row">	
			<div class="col-md-12">
				<table class="table">					
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
								@if ($value->user_id==-1)
									<td>System</td>	
								@else
									<td>{{$value->username}}</td>	
								@endif								
								<td>{{ date('Y-m-d H:i A', strtotime($value->date))}}</td>
							</tr>																							
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		 <div class="row">
            <div class="col-md-12">   
            
                <?php 					
					if (isset($filter["from"])){
						$report->appends(['from' => $filter["from"]]);		
					}
					if (isset($filter["to"])){
						$report->appends(['to' => $filter["to"]]);		
					}
				?>			            
            {!! $report->render() !!}
            </div>
	 </div>	    	
	 </div>	    	
</div>

</body>
</html>

