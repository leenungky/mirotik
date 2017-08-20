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
		 @if(Session::has('error'))
            <div class="row">
                <div class="col-md-12 alert alert-danger">      
                    <ul>
                        <li>{!! Session::get('error') !!}</li>                      
                    </ul>
                </div>
            </div>
            <br/>
        @endif    
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
		    <br/>
		@endif 	 
		
		<div class="row">				
			<div class="col-md-12">		
				<form method="post" action="/customer/createmanagement" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">username</label>
						 <input type="text" class="form-control" id="name" name="name" placeholder="input username" value="{{ old('name') }}" required>
					</div>							
					<button type="submit" class="btn btn-primary">Submit</button>
					<a href="/customer/list" class="btn btn-primary">Cancel</a>
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