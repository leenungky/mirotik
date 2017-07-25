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
		@if (count($errors))     
			<div class="row">				
				<div class="col-md-12 alert alert-danger">		
				    <ul>
				        @foreach($errors->all() as $error) 		            				            
				            <li>{{str_replace("name","Nama toko",$error)}}</li>
				        @endforeach 
				    </ul>
			    </div>
		    </div>
		@endif 		 
		<br/>
		<div class="row">				
			<div class="col-md-12">		
				<form method="post" action="/customer/create" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">Nama Toko</label>
						 <input type="text" class="form-control" id="name" name="name" placeholder="input nama" value="{{ old('name') }}" required>
					</div>					
					<div class="form-group">
					    <label for="email">Owner/Pemilik</label>
						 <input type="text" class="form-control" id="owner" name="owner" placeholder="input pemilik" value="{{ old('owner') }}" required>
					</div>					
					<div class="form-group">
					    <label for="pwd">Email:</label>
					    <input type="text" class="form-control" name="email" placeholder="input multiple email with comma" value="{{ old('email') }}" required>
					</div>
					<div class="form-group">
					    <label for="pwd">Telephone:</label>
					    <input type="text" class="form-control" name="phone" placeholder="input phone" value="{{ old('phone') }}" required>
					</div>
					<div class="form-group">
					    <label for="pwd">Address:</label>
					    <textarea name="address" class="form-control" rows="3" minlength="30" value="{{ old('address') }}" placeholder="input address min 30 character" required>{{trim(old('address'))}}</textarea>
					</div>				
					<div class="form-group">
					    <label for="pwd">Discount:</label>
					    <input type="text" class="form-control" name="discount" placeholder="input discount (numeric)" value="{{ old('discount') }}" required>
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
		$( "input[name=name]" ).focus();
	});
</script>