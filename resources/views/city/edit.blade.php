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
			<div class="col-md-12">		
				<form method="post" action="/cities/update/{{$city->id}}" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">Code</label>
						 <input type="text" class="form-control" name="code" value="{{$city->code}}" placeholder="input nama" required>
					</div>				
					<div class="form-group">
					    <label for="email">Nama</label>
						 <input type="text" class="form-control" id="city_name" name="name" value="{{$city->name}}" placeholder="input pemilik" required>
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
		
	});
</script>