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
			<form action="/customer" method="get">
				<div class="col-md-1">
					Nama<br/>
					<input type="text" name="name" class="form-control" value="{{isset($filter["name"]) ? $filter["name"] : ""}}">
				</div>
				<div class="col-md-1">
					Owner<br/>
					<input type="text" name="owner" class="form-control" value="{{isset($filter["owner"]) ? $filter["owner"] : ""}}">
				</div>
				<div class="col-md-1">
					Email<br/>
					<input type="text" name="email" class="form-control" value="{{isset($filter["email"]) ? $filter["email"] : ""}}">
				</div>
				<div class="col-md-1">
					Phone<br/>
					<input type="text" name="phone" class="form-control" value="{{isset($filter["phone"]) ? $filter["phone"] : ""}}">
				</div>
				<div class="col-md-2">
					Alamat<br/>
					<input type="text" name="address" class="form-control" value="{{isset($filter["address"]) ? $filter["address"] : ""}}">
				</div>
				<div class="col-md-2">
					<br/>
					<input type="submit" value="find" class="btn">
				</div>
			</form>
		</div>
		<br/>
		<div class="row">	
			<div class="col-md-12">
			<a href="/customer/new">Create</a>
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
						<th width="120px"><a href="/customer?sort=nama{{$str_parameter}}">Nama</a>
			    			{!!isset($arrow_nama) ? $arrow_nama : ""!!}
			    		</th>
			    		<th width="120px"><a href="/customer?sort=owner{{$str_parameter}}">Owner</a>
			    			{!!isset($arrow_owner) ? $arrow_owner : ""!!}
			    		</th>
			    		<th width="120px"><a href="/customer?sort=email{{$str_parameter}}">Email</a>
			    			{!!isset($arrow_email) ? $arrow_email : ""!!}
			    		</th>		
			    		<th width="120px"><a href="/customer?sort=discount{{$str_parameter}}">Discount</a>
			    			{!!isset($arrow_discount) ? $arrow_discount : ""!!}
			    		</th>
			    		<th width="120px"><a href="/customer?sort=phone{{$str_parameter}}">Telephon</a>
			    			{!!isset($arrow_phone) ? $arrow_phone : ""!!}
			    		</th>
			    		<th width="250px"><a href="/customer?sort=address{{$str_parameter}}">Alamat</a>
			    			{!!isset($arrow_address) ? $arrow_address : ""!!}
			    		</th>						
			    		<th width="150px"><a href="/customer?sort=created{{$str_parameter}}">Created At</a>
			    			{!!isset($arrow_created) ? $arrow_created : ""!!}
			    		</th>																		
						<th>Action</th>
					</thead>
					<tbody>
						@foreach ($cust as $key => $value)
							<tr>
								<td>{{$value->name}}</td>
								<td>{{$value->owner}}</td>
								<td>{{$value->email}}</td>
								<td>{{$value->discount}} %</td>
								<td>{{$value->phone}}</td>
								<td>{{$value->address}}</td>
								<td>{{$value->created_at}}</td>								
								<td>
									<a href="customer/edit/{{$value->id}}">
										<span class="edit"> 
					    					<span class="glyphicon glyphicon-pencil"></span>
					    				</span>
				    				</a> | 
				    				<a href="customer/delete/{{$value->id}}" class="confirmation">
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