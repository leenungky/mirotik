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
			<div class="col-md-12">		
				<form method="post" action="/customer/update/{{$customer->id}}" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">Nama</label>
						 <input type="text" class="form-control" name="name" value="{{$customer->name}}" placeholder="input nama" required>
					</div>				
					<div class="form-group">
					    <label for="email">Owner/Pemilik</label>
						 <input type="text" class="form-control" name="owner" value="{{$customer->owner}}" placeholder="input pemilik" required>
					</div>					
					
					<div class="form-group">
					    <label for="pwd">Email:</label>
					    <input type="text" class="form-control email" value="{{$customer->email}}" placeholder="input multiple email with comma" required>
					</div>
					<div class="form-group">
					    <label for="pwd">Address:</label>
					    <textarea name="address" class="form-control" rows="3" required  placeholder="input address minimum 30 character">{{$customer->address}}</textarea>
					</div>				
					<div class="form-group">
					    <label for="pwd">Telephone:</label>
					    <input type="text" class="form-control" name="phone" value="{{$customer->phone}}" placeholder="input Telephone" required>
					</div>
					
					<div class="form-group">
					    <label for="pwd">Discount:</label>
					    <input type="text" class="form-control" name="discount" value="{{$customer->discount}}" placeholder="input discount" required>
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
	// $(document.ready(function(){
		
	// }))
</script>