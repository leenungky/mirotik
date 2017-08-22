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
				<form method="post" action="/customer/create" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">Kamar</label>
						 <input type="text" class="form-control" id="name" name="name" placeholder="input no kamar" value="{{ old('name') }}" required>
					</div>							
					<div class="cls_room">
						<div class="form-group">
						    <label for="email">Ruangan</label>
						   	<input type="text" class="form-control" id="room" name="room" placeholder="input Room" value="{{ old('room') }}" required>	
						</div>						
						<div class="form-group">
						    <label for="email">Tanggal Checkout</label>
							<input type="text" class="form-control datepicker" id="checkout" name="checkout" placeholder="input Checkout" value="{{ old('checkout') }}" required>
						</div>
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

		 var projects = [
		      @foreach ($room as $key => $value)
		      	{value: "{{$value->name}}",label: "{{$value->name}}"},
		      @endforeach
		      @foreach ($meetroom as $key => $value)
		      	{value: "{{$value->name}}",label: "{{$value->name}}"},
		      @endforeach
		    ];
		    $( "#room" ).autocomplete({
		      	source: projects,
		      	minLength: 2,
		      	change: function(event,ui){
		      		console.log(ui);
		    		if (ui.item == null){                    
		        		$(this).val("");
		            	return false;
		        	}
		    	}
		  //   	,open: function (event, ui) {
				//     var len = $('.ui-autocomplete > li').length;
		  //           $('.ui-autocomplete > li').css("color", function() {
		  //               return $(this).text().indexOf('+') > -1 ? 'red' : '';
		  //           });
		  //           $(this).html('Founded ' + len + ' results');
				// }
		    });
		
	});
</script>