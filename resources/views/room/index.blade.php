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
			<form action="/room/list" method="get">				
				<div class="col-md-4">
					Room Name<br/>
					<input type="text" name="name" class="form-control" value="{{isset($input["name"]) ? $input["name"] : ""}}">
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
			<a href="/room/add">Create</a>
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
						<th>No</th>
						<th>Room Name</th>
			    		<th>Description</th>			    					    		
						<th>Action</th>
						@if ($role==config("config.supervisor"))
							<th>Lock</th>
				    		<th>Hidden</th>
			    		@endif
					</thead>
					<tbody>
						@foreach ($room as $key => $value)
							<tr>								
								<td>{{$key+1}}</td>
								<td>{{$value->name}}</td>
								<td>{{$value->description}}</td>								
								<td>
									@if ($role==config("config.supervisor") || $value->islock==0)
										<a href="/room/edit/{{$value->id}}">
											<span class="edit"> 
						    					<span class="glyphicon glyphicon-pencil"></span>
						    				</span>
					    				</a> | 
					    				<a href="/room/delete/{{$value->id}}" class="confirmation">
						    				<span class="delete">
					    						<span class="glyphicon glyphicon-remove"></span>
					    					</span> 
				    					</a>									
									@endif			    					
								</td>
								@if ($role==config("config.supervisor"))
									<td>
										@if ($value->islock==1)
											<input type="checkbox" class="lock" attr-val="{{$value->id}}" checked>
										@else
											<input type="checkbox" class="lock" attr-val="{{$value->id}}">
										@endif
									</td>
									<td>
										@if ($value->ishidden==1)
											<input type="checkbox" class="hiden" attr-val="{{$value->id}}" checked>
										@else
											<input type="checkbox" class="hiden" attr-val="{{$value->id}}">
										@endif
										
									</td>
								@endif
							</tr>																							
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	 </div>	    	
</div>
<!-- <div class="row" id="printableArea" style="display: none;">
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
   	</div> -->

</body>
</html>

 <script type="text/javascript">	
	
   $(document).ready(function(){
   		$(".lock").click(function(){
   			if ($(this).is(':checked')){	
   				var id = $(this).attr("attr-val");
   				var url = domain + "/room/lock?id=" + id + "&islock=1";				
				setData(url);
   			}else{
   				var id = $(this).attr("attr-val");
   				var url = domain + "/room/lock?id=" + id + "&islock=0";				
				setData(url);
   			}
   		});

   		$(".hiden").click(function(){
   			if ($(this).is(':checked')){	
   				var id = $(this).attr("attr-val");
   				var url = domain + "/room/hidden?id=" + id + "&ishidden=1";				
				setData(url);
   			}else{
   				var id = $(this).attr("attr-val");
   				var url = domain + "/room/hidden?id=" + id + "&ishidden=0";				
				setData(url);
   			}
   		})
   });

   function setData(url){
   		$.ajax({
			url: url,
			dataType: 'json',
			success: function(result) {			  	
			   	console.log(result.response.code);					   	
			   	if (result.response.code != 200){		   		
			   		$(this).attr('checked', false);			
				}
			}
		})
   }
	// $('.print').click(function(e) { // catch the form's submit event		
	// 	var name = $(this).attr("val-name");
	// 	var password = $(this).attr("val-password");
	// 	var url = "/customer/print?name="+ name +"&password=" + password; // the script where you handle the form input.		
	//     $.ajax({
	//            type: "GET",
	//            url: url,
	//            data: $(this).serialize(), // serializes the form's elements.
	//            success: function(result){
	//            		if (result.response.code=="200"){
	//            			console.log(result);
	//            			$(".spancode").text("");
	//            			if (result.qrcode!=""){
	//            				$("#qrcode").attr("src","data:image/png;base64," + result.qrcode);					           			
	//            			}	           			
	//            			$(".val-name").text(result.data.name);
	//            			$(".val-password").text(result.data.password);
						
	//            			setTimeout(function(){
	// 					  printDivIcon('printableArea');
	// 					}, 1000);
	//            		}
	//            }
	//         });		
	// 	return false;	    
	// });

</script> 