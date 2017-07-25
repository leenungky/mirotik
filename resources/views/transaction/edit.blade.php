<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     @include('head')
     <script type="text/javascript">
		var trans_price = {{isset($kecamatan->regular_price)? $kecamatan->regular_price : ""}};
	</script>
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
			<div class="col-md-12">		
				<form method="post" action="/transaction/update/{{$transaction->id}}" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="sender_id" value="{{isset($customer->id) ? $customer->id : ""}}">
					<input type="hidden" name="city_id" value="{{isset($kecamatan->id) ? $kecamatan->id : ""}}">
					<div class="form-group">
					    <label for="email">Pelanggan</label>
						 <input type="text" class="form-control" id="receive" name="receive" value="{{$customer->name}}" required>
					</div>
					<div class="form-group">
					    <label for="email">Reseller</label>
						 <input type="text" class="form-control" placeholder="input reseller" id="reseller" name="reseller" value="{{$transaction->reseller}}" required>
					</div>
					<div class="form-group">
					    <label for="email">Penerima</label>
						 <input type="text" class="form-control" id="receive" placeholder="input penerima" name="receive" value="{{$transaction->receipt_name}}" required>
					</div>
					<div class="form-group">
					    <label for="email">Phone</label>
						 <input type="text" class="form-control" id="phone" name="phone" value="{{$transaction->phone}}" required>
					</div>
					<div class="form-group">
					    <label for="pwd">Detail Alamat:</label>
					    <textarea name="address" id="address" class="form-control" rows="3" required>{{$transaction->address}}</textarea>
					</div>							
					<div class="form-group">
					    <label for="pwd">City:</label>					    
					    <p id="cityinput"><input type="text" class="form-control" id="city" name="City" value="{{isset($kecamatan->city) ? $kecamatan->city : ""}}, {{isset($kecamatan->kecamatan) ? $kecamatan->kecamatan : ""}}" required></p>
					</div>
					<div class="form-group">
					    <label for="email">Weight</label>
						 <input type="text" class="form-control" id="weight" min=1 name="weight" value="{{$transaction->weight}}" required>
					</div>														
					<div class="form-group">
					    <label for="email">Price</label>
						 <input type="text" class="form-control" id="price" name="price" value="{{$transaction->price}}" required>
					</div>					
					<button type="submit" class="btn">Submit</button>		
				</form>
			</div>
		</div>
	 </div>	    	
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