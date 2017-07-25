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
				<form method="post" action="/transaction/createtotal" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="sender_id" required>					
					<div class="form-group">
					    <label for="email">Pelanggan</label>
						 <input type="text" class="form-control" id="sender" name="sender" placeholder="input pelanggan autocomplete" required>
					</div>					
					<div class="form-group">
					    <label for="email">Total parcel</label>
						 <input type="text" class="form-control" id="total" name="total" placeholder="input total (numeric)" required>
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
		$( "input[name=sender]" ).focus();
	})
</script>