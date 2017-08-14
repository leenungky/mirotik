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
			    					<a href="javascript:void(0)" class="print" val="{{$value[".id"]}}" val-name="{{$value["name"]}}" val-password="{{isset($value["password"]) ? $value["password"] : ""}}">
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
<div class="row" id="printableArea" style="display: none">
		<div class="left" style="width: 140px; font-size: 8px; margin-left: 10px">
			<div><br/>
				<div style="width: 100px;margin-left: 50px;margin-right: 50px;margin-bottom:20px; ">
			   		<img src="{{ URL::asset('img/wifi.png') }}" width="30px">    			
				</div>
				<div style="font-size: 11px;font-weight: bold; border-top:1px solid orange; padding: 10px;">
					<span class="txt-attr">USERNAME</span> : <span class="val-name"></span>
				</div>				
				<div style="font-size: 11px;font-weight: bold; border-top:1px solid orange; border-bottom: 1px solid orange; padding: 10px;">
					<span class="txt-attr">PASSWORD</span> : <span class="val-password"></span>
				</div>						
				
				<span id="qr-name" style="font-weight: bold;"></span><br/><br/>
			</div>
    	</div>
    	<div class="left" style="width:20px">
    		&nbsp;
    	</div>
    	<div class="left" style="text-align: center;width: 130px; margin: 20px;">    		
    		<div>    				
    			<img src="{{ URL::asset('img/cabin1.jpg') }}" width="100px">    			
    		</div>
    		<div style="width: 100px;margin: 10px;">
			   	<img src="" alt="barcode" id="qrcode" />				   	
			</div>
    	</div>
    	<div style="clear: both;"></div>    		    	
    	<div class="left" style="width: 140px;font-size: 7px; margin-left: 10px; margin-right: 10px;">    					
			<p style="font-size: 14px;font-weight: bold;">
				http://cabinhotel.co.id
			</p>
    	</div>
    	<div class="left" style="width:20px">
    	&nbsp;
    	</div>
    	<div class="left" style="width: 130px;font-size: 7px; margin-left: 10px; margin-right: 10px;background-color: orange !important;height: 30px;">		
    	</div>

    	<div style="clear: both;"></div><br/>    		
   	</div>
</body>
</html>

<script type="text/javascript">	
	$(document).ready(function(){
		$('.print').click(function(e) { // catch the form's submit event		
			var name = $(this).attr("val-name");
			var password = $(this).attr("val-password");
			var url = "/customer/print?name="+ name +"&password=" + password; // the script where you handle the form input.		
		    $.ajax({
		           type: "GET",
		           url: url,
		           data: $(this).serialize(), // serializes the form's elements.
		           success: function(result){
		           		if (result.response.code=="200"){
		           			console.log(result);
		           			$(".spancode").text("");
		           			if (result.qrcode!=""){
		           				$("#qrcode").attr("src","data:image/png;base64," + result.qrcode);					           			
		           			}	           			
		           			$(".val-name").text(result.data.name);
		           			$(".val-password").text(result.data.password);
							
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