<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<script type="text/javascript">
		var trans_price = 0;
	</script>
     @include('head')
     <style type="text/css" media="print">
     	   @media print {
			    @page { margin: 0px 6px; }
  				body  { margin: 0px 6px; }   					  
  				#printableArea { display: block; }
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
			<div class="col-md-10">		
				Total parcel : {{$total_parcel->total}}, Total parcel transaction  :{{$total_transaction}}
			</div>
			<div class="col-md-2">
				<a href="/transaction/cancel?total_parcel_id={{$total_parcel->id}}" class="btn btn-primary">
					Cancel
				</a>
			</div>
		</div>

		<div class="row">	
			<div class="col-md-12">		
				<form method="post" action="/transaction/create" id="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="sender_id" value="{{$customer->id}}">
					<input type="hidden" name="city_id">
					<div class="form-group">
					    <label for="email">Pelanggan</label>
						 <input type="text" class="form-control" id="sender" name="sender" value="{{$customer->name}}" required disabled>
					</div>
					<div class="form-group">
					    <label for="email">Reseller</label>
						 <input type="text" class="form-control" id="reseller" name="reseller" placeholder="input reseller" required>
					</div>
					<div class="form-group">
					    <label for="email">Penerima</label>
						 <input type="text" class="form-control" id="receive" placeholder="input penerima" name="receive" required>
					</div>
					<div class="form-group">
					    <label for="pwd">Detail Alamat:</label>
					    <textarea name="address" id="address" class="form-control" rows="3" placeholder="input address min 30 character" required></textarea>
					</div>				
					<div class="form-group">
					    <label for="pwd">City dan Kecamatan:</label>
					    <p id="cityinput"><input type="text" class="form-control" id="city" placeholder="input city autocomplete" name="City"  required></p>
					</div>
					<div class="form-group">
					    <label for="email">Phone</label>
						 <input type="text" class="form-control" id="phone" placeholder="input phone" name="phone" required>
					</div>															
					<div class="form-group">
					    <label for="email">Weight</label>
						 <input type="text" class="form-control" id="weight" placeholder="input weight" name="weight" required>
					</div>
					<div class="form-group">
					    <label for="email">Price</label>
						 <input type="text" class="form-control" id="price" name="price" placeholder="input price (numeric)" disabled required>
					</div>										
					<button type="submit" class="btn btn-submit">Submit</button>
				</form>
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
				Nama :<br/>
				<span id="qr-name" style="font-weight: bold;"></span><br/><br/>
				Telepon :<br/>
				<span style="font-size: 11px;font-weight: bold;" id="qr-phone"></span><br/>
				Berat : <span id="qr-weight" style="font-size: 11px;font-weight: bold;"></span><br/><br/>
				Tujuan : <br/>
				<div id="qr-full-address" style="font-size: 8px;;font-weight: bold;">
				</div><br/>
				Kecamatan : <br/>
				<div id="qr-kecamatan" style="font-size: 8px;;font-weight: bold;">
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
			Untuk keluhan anda bisa menghubungi customer service kami 021-2902 2537 hari senin-sabtu jam 08.00-17.00 atau melalui email : daniel@bintangkiriman.com
    	</div>    	
    	<div style="clear: both;"></div><br/>    		
   	</div>
</body>
</html>
<script type="text/javascript">	
	$(document).ready(function(){
		$("p").focusin(function() {		    		    
		    $('html,body').animate({ scrollTop: $(this).offset().top }, 'slow');
		    
		});
		$( "input[name=reseller]" ).focus();

		$('#formsubmit').submit(function(e) { // catch the form's submit event		
			$(".btn-submit").hide();
			var weight = $("input[name=weight]").val();
			if (weight<=0){
				$("input[name=weight]").focus();
				alert("Weight harus lebih dari nol");
				$(".btn-submit").show();
				return false;
			}		
			
			var url = "/transaction/createajax"; // the script where you handle the form input.		
		    $.ajax({
		           type: "POST",
		           url: url,
		           data: $(this).serialize(), // serializes the form's elements.
		           success: function(result){
		           		if (result.response.code=="200"){		           			
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
	});  
	

</script>