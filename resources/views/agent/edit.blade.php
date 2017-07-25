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
				<form method="post" action="/agent/update/{{$agent->id}}" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">nama</label>
						 <input type="text" class="form-control" name="name" value="{{$agent->name}}" placeholder="input nama" required>
					</div>				
					<div class="form-group">
					    <label for="email">City</label>
						<select name="city" class="form-control" required>
							<option>Pilih City</option>
							@foreach ($cities as $key => $value)
								@if ($value->id==$agent->city_id)
									<option value="{{$value->id}}" selected>{{$value->name}}</option>
								@else
									<option value="{{$value->id}}">{{$value->name}}</option>
								@endif
							@endforeach
						</select>
					</div>	
					<div class="form-group">
					    <label for="email">Phone</label>
						 <input type="text" class="form-control" id="phone" name="phone" placeholder="input phone" value="{{$agent->phone}}" required>
					</div>		
					<div class="form-group">
					    <label for="email">Address</label>
					    <textarea name="address" cols="3" class="form-control" placeholder="input address" required>{{$agent->address}}</textarea>						 
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