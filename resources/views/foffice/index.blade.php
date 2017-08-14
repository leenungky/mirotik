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
						<th>Profile</th>
						<th>Action</th>
					</thead>
					<tbody>
						@if (isset($usermkr))
						@foreach ($usermkr as $key => $value)
							<tr>
								<td>{{($key+1)}}</td>
								<td>{{$value["name"]}}</td>								
								<td>{{isset($value["password"]) ? $value["password"] : ""}}</td>								
								<td>{{isset($value["profile"]) ? $value["profile"] : ""}}</td>								
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
<div class="row" id="printableArea">
		<div class="left" style="width: 230px; font-size: 8px; margin-left: 10px">
			<div><br/>
				<div style="font-size: 11px;font-weight: bold; border-top:1px solid orange">
					USERNAME : test123
				</div>				
				<div style="font-size: 11px;font-weight: bold; border-top:1px solid orange">
					<span style="color: orange">PASSWORD</span> : 12345
				</div>						
				
				<span id="qr-name" style="font-weight: bold;"></span><br/><br/>
			</div>
    	</div>
    	<div class="left" style="text-align: center;width: 100px;">
    		<br/>
    		<div>    				
    			<span style="font-size: 13px;font-weight: bold;">BintangKiriman</span><br/>
    			<span style="font-size: 11px;">Scan barcode ini :</span>
    		</div>
    		<div style="width: 100px;padding: 10px 0px;">
			   	<img src="" alt="barcode" id="qrcode" />				   	
			</div>
    	</div>
    	<div style="clear: both;"></div>    		
    	<div class="left" style="width: 350px; margin-left: 10px">
    		--------------------------------------------------------------------------
    	</div>
    	<br/>
    	<div style="clear: both;"></div>
    	<div class="left full-desc" style="width: 350px;font-size: 7px; margin-left: 10px; margin-right: 10px;">    					

			<p>
				http:/cabinhotel.co.id
			</p>
    	</div>

    	<div style="clear: both;"></div><br/>    		
   	</div>
</body>
</html>

<script type="text/javascript">	
	
   
	$('.print').click(function(e) { // catch the form's submit event		
		var order_no = $(this).attr("val");
		var url = "/customer/print?order_no=aaaa"; // the script where you handle the form input.		
	    $.ajax({
	           type: "GET",
	           url: url,
	           data: $(this).serialize(), // serializes the form's elements.
	           success: function(result){
	           		if (result.response.code=="200"){
	           			console.log(result);
	           			$(".spancode").text("");
	           			$("#qrcode").attr("src","data:image/png;base64," + result.qrcode);					           			
	           			$("#label-dest").text("Alamat");
						$("#qr-name").text("");
						$("#qr-phone").text("");
						$("#qr-full-address").html("");
						$("#qr-kecamatan").html("");
						$("#qr-weight").text("");            			
	           			setTimeout(function(){
						  printDivIcon('printableArea');
						}, 1000);
	           		}
	           }
	        });		
		return false;	    
	});

</script>