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
					    <label for="email">Username</label>
						 <input type="text" class="form-control" id="name" name="name" placeholder="input name" value="{{ old('name') }}" required>
					</div>		
					<div class="form-group">
					    <label for="email">Password</label>
						 <input type="text" class="form-control" id="password" name="password" placeholder="input password" value="{{ old('password') }}" required>
					</div>
					<div class="form-group">
					    <label for="email">Profile</label>
						 <select name="profile" class="form-control">
						 	@if ($role=="administrator")							 	
							 	<option>room_profile</option>
							 	<option>management_profile</option>
								<option>meeting_profile</option>
							@else
								<option>room_profile</option>
								<option>meeting_profile</option>
						 	@endif 
						 </select>
					</div>	
					<div class="cls_room" style="display: none;">
						<div class="form-group">
						    <label for="email">Dari</label>
							 <input type="text" class="form-control datetimepicker" id="from" name="from" placeholder="input dari" value="{{ old('from') }}">
						</div>
						<div class="form-group">
						    <label for="email">To</label>
							 <input type="text" class="form-control datetimepicker" id="to" name="to" placeholder="input to" value="{{ old('to') }}">
						</div>
						<div class="form-group">
						    <label for="email">Room</label>
							 <input type="text" class="form-control" id="room" name="room" placeholder="input room" value="{{ old('room') }}">
						</div>						
						<div class="form-group">
						    <label for="email">Total Days</label>
							 <input type="text" class="form-control " id="day" name="day" placeholder="input total day" value="{{ old('day') }}">
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
		@if (old("profile")=="room_profile")
			$(".cls_room").show();
		@endif
		$("select[name='profile']").change(function(){
			if ($(this).val()=="room_profile"){
				$("input[name='from']").prop("required", true);
				$("input[name='to']").prop("required",true);
				$("input[name='room']").prop("required",true);		
				$("input[name='day']").prop("required",true);		
				$(".cls_room").show();
			}else{
				$("input[name='from']").removeProp("required");
				$("input[name='from']").val("");
				$("input[name='to']").val("");
				$("input[name='to']").removeProp("required");
				$("input[name='room']").val("");
				$("input[name='room']").removeProp("required");
				$("input[name='day']").val("");
				$("input[name='day']").removeProp("required");

				$(".cls_room").hide();
			}
		});
	});
</script>