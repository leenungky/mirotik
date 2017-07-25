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
				<form method="post" action="/user/create" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">									
					<div class="form-group">
					    <label for="email">Firstname</label>
						 <input type="text" class="form-control" id="firstname" name="firstname" placeholder="input username" value="{{ old('firstname') }}" required>
					</div>					
					<div class="form-group">
					    <label for="email">Lastname</label>
						 <input type="text" class="form-control" id="lastname" name="lastname" placeholder="input username" value="{{ old('lastname') }}" required>
					</div>					
					<div class="form-group">
					    <label for="email">email</label>
						 <input type="text" class="form-control" id="email" name="email" placeholder="input email" value="{{ old('email') }}" required>
					</div>
					<div class="form-group role-user">
					    <label for="email">Role User</label>
						<select name="role" class="form-control" required>
							<option>Pilih Role</option>
							@foreach ($role as $key => $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group agent-user">
					    <label for="email">Agent</label>
						<select name="agent" id="agent" class="form-control">
							<option>Pilih Agent</option>
							@foreach ($agent as $key => $value)
								<option value="{{$value->id}}">{{$value->name}}, {{$value->kota}}</option>
							@endforeach
						</select>
					</div>	
					<div class="form-group">
					    <label for="pwd">Password:</label>
					    <input type="password" class="form-control" name="password" placeholder="input password" value="{{ old('password') }}" required>
					</div>
					<div class="form-group">
					    <label for="pwd">Password Confirmation:</label>
					    <input type="password" class="form-control" name="password_confirmation" placeholder="input password" value="{{ old('password_confirmation') }}" required>
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
		$("select[name='role']").change(function(){
			var role = $("select[name='role'] option:selected").text();
			if (role=="staff"){
				$(".agent-user").show();
				$("#agent" ).attr( "required", "true" );
			}else{
				$(".agent-user").hide();				
				$("#agent").val($("#agent option:first").val());				
				$("#agent" ).attr( "required", "false" );
			}
		})
	});
</script>