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
				<form method="post" action="/customer/update/{{$usermkr[".id"]}}" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">Username</label>
						 <input type="text" class="form-control" id="name" name="name" placeholder="input name" value="{{$usermkr["name"]}}" required>
					</div>		
					<div class="form-group">
					    <label for="email">Password</label>
						 <input type="text" class="form-control" id="password" name="password" placeholder="input password" value="{{ $usermkr["password"] }}" required>
					</div>								
					<div class="form-group">
					    <label for="email">Profile</label>
						 <select name="profile" class="form-control">
						 	@if ($role=="administrator")							 	
						 		@if ($usermkr["profile"]=="room_profile")
								 	<option selected>room_profile</option>
							 	@else
							 		 <option>room_profile</option>
							 	@endif

							 	@if ($usermkr["profile"]=="management_profile")
								 	<option selected>management_profile</option>
							 	@else
							 		 <option>management_profile</option>
							 	@endif

							 	@if ($usermkr["profile"]=="meeting_profile")								 	
								 	<option selected>meeting_profile</option>
							 	@else
							 		 <option>meeting_profile</option>
							 	@endif
							@else
								@if ($usermkr["profile"]=="room_profile")
								 	<option selected>room_profile</option>
							 	@else
							 		 <option>room_profile</option>
							 	@endif
							 	@if ($usermkr["profile"]=="meeting_profile")								 	
								 	<option selected>meeting_profile</option>
							 	@else
							 		 <option>meeting_profile</option>
							 	@endif
						 	@endif 
						 </select>
					</div>		
					<div class="cls_room" style="display: none;">
						
						<div class="form-group">
						    <label for="email">Room</label>
							  <select name="room_id" class="form-control">
						    	<option value="">Pilih Room</option>
						    	@foreach ($room as $key => $value)
						    		@if (in_array($roomdb->room_id, $arr_room_id_use))
						    			@if ($roomdb->room_id==$value->id)
						    				<option value="{{$value->id}}" style="color:red; font-weight: bold;" selected>{{$value->name}}</option>
						    			@else
						    				<option value="{{$value->id}}" style="color:red; font-weight: bold;">{{$value->name}}</option>
						    			@endif
						    		@else
						    			@if ($roomdb->room_id==$value->id)
						    				<option room_id="{{$value->id}}" selected>{{$value->name}}</option>
						    			@else
						    				<option value="{{$value->id}}">{{$value->name}}</option>
						    			@endif
						    			
						    		@endif
						    		
						    	@endforeach
						    </select>	
						</div>						
						<div class="form-group">
						    <label for="email">Total Days</label>
							 <input type="text" class="form-control " id="day" name="day" placeholder="input total day" value="{{$roomdb->day}}">
						</div>
					</div>								
					<div class="cls_meetroom" style="display: none;"> 
						<div class="form-group">
							    <label for="email">Meeting Room Name</label>
								<select name="meetroom_id" class="form-control">
							    	<option value="">Pilih Meeting Room</option>
							    	@foreach ($meetroom as $key => $value)
							    		@if (in_array($value->id, $arr_meetroom_id_use))
							    			@if ($roomdb->meetroom_id==$value->id)
							    				<option value="{{$value->id}}" style="color:red; font-weight: bold;" selected>{{$value->name}}</option>
							    			@else
							    				<option value="{{$value->id}}" style="color:red; font-weight: bold;">{{$value->name}}</option>
							    			@endif
							    		@else
							    			@if ($roomdb->meetroom_id==$value->id)
							    				<option value="{{$value->id}}" selected>{{$value->name}}</option>
							    			@else
							    				<option value="{{$value->id}}">{{$value->name}}</option>
							    			@endif						    			
							    		@endif						    		
							    	@endforeach
							    </select>							 
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
		@if ($usermkr["profile"]=="room_profile")
			$(".cls_room").show();
		@elseif ($usermkr["profile"]=="meeting_profile")
			$(".cls_meetroom").show();
		@endif
		$("select[name='profile']").change(function(){
			if ($(this).val()=="room_profile"){
				hide();
				$("input[name='from']").prop("required", true);
				$("input[name='to']").prop("required",true);
				$("input[name='room']").prop("required",true);		
				$("input[name='day']").prop("required",true);		
				$(".cls_room").show();
			}else if ($(this).val()=="meeting_profile"){
				hide();
				$("select[name='meetroom_id']").prop("required",true);		
				$(".cls_meetroom").show();
			}else{
				hide();
			}
		});
	});

	function hide(){
		$("input[name='from']").removeProp("required");
		$("input[name='from']").val("");
		$("input[name='to']").val("");
		$("input[name='to']").removeProp("required");
		$("select[name='room_id']").val("");
		$("select[name='room_id']").removeProp("required");
		$("input[name='day']").val("");
		$("input[name='day']").removeProp("required");
		$(".cls_room").hide();

		$("select[name='meetroom_id']").val("");
		$("select[name='meetroom_id']").removeProp("required");
		$(".cls_meetroom").hide();
	}
</script>