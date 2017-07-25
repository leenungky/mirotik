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
			<form action="/transaction" method="get">
				<div class="col-md-1">
					Pelanggan<br/>
					<input type="text" name="pelanggan" class="form-control" value="{{isset($input["name"]) ? $input["name"] : ""}}">
				</div>
				<div class="col-md-1">
					Penerima<br/>
					<input type="text" name="penerima" class="form-control" value="{{isset($input["penerima"]) ? $input["penerima"] : ""}}">
				</div>
				<div class="col-md-1">
					AWB<br/>
					<input type="text" name="awb" class="form-control" value="{{isset($input["awb"]) ? $input["awb"] : ""}}">
				</div>
				<div class="col-md-2">
					City/kecamatan<br/>
					<input type="text" name="kecamatan" class="form-control" value="{{isset($input["kecamatan"]) ? $input["kecamatan"] : ""}}">
				</div>				
				<div class="col-md-2">
					address<br/>
					<input type="text" name="address" class="form-control" value="{{isset($input["address"]) ? $input["address"] : ""}}">
				</div>
				<div class="col-md-1">
					phone<br/>
					<input type="text" name="phone" class="form-control" value="{{isset($input["phone"]) ? $input["phone"] : ""}}">
				</div>
				<div class="col-md-1">
					From<br/>
					<input type="text" name="from" class="form-control datepicker" value="{{isset($input["from"]) ? $input["from"] : ""}}">
				</div>
				<div class="col-md-1">
					to<br/>
					<input type="text" name="to" class="form-control datepicker" value="{{isset($input["to"]) ? $input["to"] : ""}}">
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
			<a href="/transaction/newtotal">Create</a>
			</div>
		</div>
		<br/>
		<div class="row">	
			<div class="col-md-12">
				<table class="table table-transaction">
					<thead>
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

						<th width="130px"><a href="/transaction?sort=pelanggan{{$str_parameter}}">Pelanggan</a>
			    			{!!isset($arrow_pelanggan) ? $arrow_pelanggan : ""!!}
			    		</th>
			    		<th width="120px"><a href="/transaction?sort=reseller{{$str_parameter}}">Reseller</a>
			    			{!!isset($arrow_reseller) ? $arrow_reseller : ""!!}
			    		</th>		
						<th width="120px"><a href="/transaction?sort=penerima{{$str_parameter}}">Penerima</a>
			    			{!!isset($arrow_penerima) ? $arrow_penerima : ""!!}</th>
			    		<th width="120px"><a href="/transaction?sort=awb{{$str_parameter}}">AWB</a>
			    			{!!isset($arrow_awb) ? $arrow_awb : ""!!}</th>
			    		<th width="120px"><a href="/transaction?sort=city{{$str_parameter}}">City</a>
			    			{!!isset($arrow_city) ? $arrow_city : ""!!}</th>
			    		<th width="130px"><a href="/transaction?sort=kecamatan{{$str_parameter}}">Kecamatan</a>
			    			{!!isset($arrow_kecamatan) ? $arrow_kecamatan : ""!!}</th>			    		
			    		<th width="250px"><a href="/transaction?sort=address{{$str_parameter}}">Address</a>
			    			{!!isset($arrow_address) ? $arrow_address : ""!!}</th>
			    		<th width="120px"><a href="/transaction?sort=phone{{$str_parameter}}">Phone</a>
			    			{!!isset($arrow_phone) ? $arrow_phone : ""!!}</th>
						<th width="100px"><a href="/transaction?sort=weight{{$str_parameter}}">Weight</a>
			    			{!!isset($arrow_weight) ? $arrow_weight : ""!!}</th>						
			    		<th width="100px"><a href="/transaction?sort=harga{{$str_parameter}}">Harga</a>
			    			{!!isset($arrow_harga) ? $arrow_harga : ""!!}</th>						
			    		<th width="150px"><a href="/transaction?sort=created_at{{$str_parameter}}">Created At</a>
			    			{!!isset($arrow_created_at) ? $arrow_created_at : ""!!}</th>												
						<th width="150px">Action</th>
					</thead>
					<tbody>
						@foreach ($trans as $key => $value)
							<tr>
								<td>{{$value->name}}</td>
								<td>{{$value->reseller}}</td>
								<td>{{$value->receipt_name}}</td>
								<td>{{$value->order_no}}</td>
								<td>{{$value->city}}</td>
								<td>{{$value->kecamatan}}</td>
								<td>{{$value->address}}</td>
								<td>{{$value->phone}}</td>
								<td>{{$value->weight}}</td>
								<td style="text-align: right;width: 100px">Rp {{number_format($value->price)}}</td>
								<td>{{$value->created_at}}</td>								
								<td>
									<a href="/transaction/edit/{{$value->id}}">
										<span class="edit"> 
					    					<span class="glyphicon glyphicon-pencil"></span>
					    				</span>
				    				</a> | 
				    				<a href="/transaction/delete/{{$value->id}}"  class="confirmation">
					    				<span class="delete">
				    						<span class="glyphicon glyphicon-remove"></span>
				    					</span> 
			    					</a> | 
			    					<a href="javascript:void(0)" class="print" val="{{$value->order_no}}">
			    						 <span class="glyphicon glyphicon-print"></span> 
			    					</a>
								</td>
							</tr>																							
						@endforeach					
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<?php 
					if (isset($sort)){
						$trans->appends(['sort' => $sort]);		
					}
					if (isset($order_by)){
						$trans->appends(['order_by' => $order_by]);		
					}
				?>
				{!! $trans->render() !!}
			</div>
		</div>
	 </div>	    	
</div>
	<div class="row" id="printableArea" style="display: none;">
		<div class="left" style="width: 230px; font-size: 8px; margin-left: 10px">
			<div><br/>
				<div style="font-size: 11px;font-weight: bold;">Tujuan : <span id="label-dest"></span></div><br/>
				AWB : <br/>
				<span style="font-size: 11px;font-weight: bold;" class="spancode"></span><br/><br/>    				
				Nama :<br/
>				<span id="qr-name" style="font-weight: bold;"></span><br/><br/>
				Telepon :<br/>
				<span style="font-size: 11px;font-weight: bold;" id="qr-phone"></span><br/>
				Berat : <span id="qr-weight" style="font-size: 11px;font-weight: bold;"></span><br/><br/>
				Tujuan : <br/>
				<div id="qr-full-address" style="font-size: 8px;;font-weight: bold;">
				</div><br/>
				Kecamatan : <br/>
				<div id="qr-kecamatan" style="font-size: 11px;;font-weight: bold;">
				</div>
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
			Untuk keluhan anda bisa menghubungi customer service kami 021-2902 2537 hari senin-sabtu jam 08.00-17.00 atau melalui email : daneil@bintangkiriman.com
    	</div>

    	<div style="clear: both;"></div><br/>    		
   	</div>
</body>
</html>

<script type="text/javascript">	
	
   
	$('.print').click(function(e) { // catch the form's submit event		
		var order_no = $(this).attr("val");
		var url = "/transaction/findtoprint?order_no=" + order_no; // the script where you handle the form input.		
	    $.ajax({
	           type: "GET",
	           url: url,
	           data: $(this).serialize(), // serializes the form's elements.
	           success: function(result){
	           		if (result.response.code=="200"){
	           			console.log(result);
	           			$(".spancode").text(result.data.order_no);
	           			$("#qrcode").attr("src","data:image/png;base64," + result.qrcode);					           			
	           			$("#label-dest").text("Alamat");
						$("#qr-name").text(result.data.receipt_name);
						$("#qr-phone").text(result.data.phone);
						$("#qr-full-address").html(result.address);
						$("#qr-kecamatan").html(result.kecamatan);
						$("#qr-weight").text(result.data.weight);            			
	           			setTimeout(function(){
						  printDivIcon('printableArea');
						}, 1000);
	           		}
	           }
	        });		
		return false;	    
	});

</script>