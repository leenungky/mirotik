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
				            <li>{{$error}}</li>
				        @endforeach 
				    </ul>
			    </div>
		    </div>
		@endif 
		<br/>
		<div class="row">				
			<div class="col-md-12">		
				<form method="post" action="/meetroom/create" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">									
					<div class="form-group">
					    <label for="email">Meeting room</label>
						 <input type="text" class="form-control" id="name" name="name" placeholder="input meeting room" value="{{ old('name') }}" required>
					</div>					
					<div class="form-group">
					    <label for="email">Description</label>
						 <textarea  class="form-control" id="description" name="description" placeholder="input username">{{ old('description') }}</textarea> 
					</div>					
					
					<button type="submit" class="btn btn-primary">Submit</button>
					<a href="/room/list" class="btn btn-primary">Cancel</a>
				</form>
			</div>
		</div>
	</div>	    	

</div>
</body>
</html>