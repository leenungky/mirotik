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
		<div class="row">	
				<div class="col-md-12">
				<a href="/customer/add">Create Tamu</a> 
				@if ($role==config("config.supervisor"))
					| <a href="/customer/addmanagement">Create Management</a>
					| <a href="/customer/addstaff">Create Staff</a>
				@endif
				</div>
			</div>
			<br/>		
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
						<th>Nama Tamu</th>						
						<th width="120px"><a href="/customer/list?sort=room{{$str_parameter}}">No Kamar</a>
			    			{!!isset($arrow_nama) ? $arrow_nama : ""!!}
			    		</th>						
						<th>Action</th>
					</thead>
					<tbody>
						@if (isset($show))
						<?php $i=1; ?>
						@foreach ($show as $key => $value)
							<tr>
								<td>{{($i++)}}</td>
								<td>{{$value["name"]}}</td>								
								<td>{{$value["room"]}}</td>						
								<td>									
										<a href="/customer/edit/{{$value["id"]}}">
											<span class="edit"> 
						    					<span class="glyphicon glyphicon-pencil"></span>
						    				</span>
					    				</a>
					    				| 
					    				<a href="/customer/delete/{{$value["id"]}}" class="confirmation">
						    				<span class="delete">
					    						<span class="glyphicon glyphicon-remove"></span>
					    					</span> 
				    					</a>				    					 			    					
			    						
			    					| <a href="javascript:void(0)" class="print" val="{{$value["id"]}}" val-name="{{$value["room"]}}" val-password="{{$value["password"]}}" val-room="{{$value["room"]}}">
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
<div class="row" id="printableArea" style="display: none">
		<div class="left" style="width: 110px; font-size: 12px; margin-left: 10px">
			<div><br/>
				<div style="width: 100px;margin-left: 50px;margin-right: 50px;margin-bottom:20px; ">
			   		<img src="{{ URL::asset('img/wifi.png') }}" width="30px">    			
				</div>
				<div style="font-size: 12px;font-weight: bold; padding: 10px;text-align: center;">
					<span class="val-room">2002</span>
				</div>				
				<div style="font-size: 12px;font-weight: bold; border-top:1px solid orange; padding: 10px;">
					<span class="txt-attr">USER</span> : <span class="val-name"></span>
				</div>				
				<div style="font-size: 12px;font-weight: bold; border-top:1px solid orange; border-bottom: 1px solid orange; padding: 10px;">
					<span class="txt-attr">PASS</span> : <span class="val-password"></span>
				</div>						
				
				<span id="qr-name" style="font-weight: bold;"></span><br/><br/>
			</div>
    	</div>
    	<div class="left" style="width:2px">
    		&nbsp;
    	</div>
    	<div class="left" style="text-align: center;width: 100px; margin: 20px;">    		
    		<div>    				
    			<img src="{{ URL::asset('img/cabin1.jpg') }}" width="80px">    			
    		</div>
    		<div style="width: 100px;">
			   	<img src="" alt="" id="qrcode" />				   	
			</div>
    	</div>
    	<div style="clear: both;"></div>	    	
    	<div class="left" style="width: 110px;font-size: 7px; margin-left: 10px; margin-right: 10px;">    					
			<p style="font-size: 11px;font-weight: bold;">
				http://cabinhotel.co.id
			</p>
    	</div>
    	<div class="left" style="width:2px">
    	&nbsp;
    	</div>
    	<div class="left" style="width: 100px;font-size: 7px; margin-left: 10px; margin-right: 10px;background-color: orange !important;height: 30px;">		
    	</div>

    	<div style="clear: both;"></div><br/>    		
   	</div>
</body>
</html>

<script type="text/javascript">	
	$(document).ready(function(){
		// $(document).on('click', '.print', function(e) {
		// $('.print').on('click',function(){
		$('.print').click(function(e) { // catch the form's submit event		
			var name = $(this).attr("val-name");
			var password = $(this).attr("val-password");
			var room = $(this).attr("val-room");			
			var url = "/customer/print?name="+ name +"&password=" + password + "&room="  + room; 
		    $.ajax({
		           type: "GET",
		           url: url,
		           data: $(this).serialize(), // serializes the form's elements.
		           success: function(result){
		           		if (result.response.code=="200"){
		           			console.log(result);
		           			console.log(result.data);
		           			$(".spancode").text("");
		           			if (result.qrcode!=""){
		           				$("#qrcode").attr("src","data:image/png;base64," + result.qrcode);					           			
		           			}	           			
		           			$(".val-name").text(result.data.name);
		           			$(".val-password").text(result.data.password);		           			
		           			$(".val-room").text(result.data.room);
							
		           			setTimeout(function(){
							  printDivIcon('printableArea');
							}, 1000);
		           		}
		           }
		        });		
			return false;	    
		});

	})
   
	

</script>