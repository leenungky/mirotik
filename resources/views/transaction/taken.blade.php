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
		

		@if(Session::has('message')) 
			<div class="row">
				<div class="col-md-12 alert alert-info" style="display: block;">
					{!!Session::get('message')!!}
				</div>
			</div>
		@endif		
		<div class="row">				
			<div class="col-md-12">		
				<form method="post" action="/transaction/create_taken" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<div class="form-group">
					    <label for="email">Type</label>
						<select class="form-control" name="type" required>
							<option value="">Pilih Type</option>
						 	<option value="1" {{(old("type")=="1") ? "selected" : "" }}>Diterima</option>
						 	<option value="0" {{(old("type")=="0") ? "selected" : "" }}>Bermasalah</option>
						 </select>
					</div>
					<div class="form-group">
					    <label for="email">Awb</label>
						 <input type="text" class="form-control" id="awb" name="awb" placeholder="input awb" value="{{ old('awb') }}" required>
					</div>					
					<div class="form-group">
					    <label for="pwd">Tanggal:</label>
					    <input type="text" class="form-control datepicker" name="delivery_date" placeholder="input tanggal delivery" value="{{ old('delivery_date') }}">
					</div>					
					<div class="problem">
						<div class="form-group">
						    <label for="email">Description Masalah</label>
							<select class="form-control" name="description_problem">
								<option value="">Pilih Masalah</option>
							 	<option value="BA" {{(old("description_problem")=="BA") ? "selected" : "" }}>BA</option>
							 	<option value="NTH" {{(old("description_problem")=="NTA") ? "selected" : "" }}>NTH</option>
							 	<option value="CC" {{(old("description_problem")=="CC") ? "selected" : "" }}>CC</option>
							 </select>
						</div>				
					</div>	
					<div class="deliver">					
						<div class="form-group">
						    <label for="pwd">Penerima:</label>
						    <input type="text" class="form-control" name="penerima" placeholder="input penerima" value="{{ old('penerima') }}">
						</div>
						<div class="form-group">
						    <label for="pwd">Status Penerima:</label>
						    <input type="text" class="form-control" name="status" placeholder="Input Status Penerima" value="{{ old('status') }}">
						</div>						
						<div class="form-group">
						    <label for="pwd">Jam</label>
						    <div>
						    <input type="text" class="form-control jam" name="jam" placeholder="jam" value="{{ old('jam') }}" maxlength="2">
						    <label class="jam">:</label>
						    <input type="text" class="form-control jam" name="menit" placeholder="menit" value="{{ old('menit') }}" maxlength="2">
						    </div>
						</div>									
					</div>					
					<button type="submit" class="btn">Submit</button>
					<a href="/transaction" class="btn btn-primary">cancel</a>
				</form>
			</div>
		</div>
	</div>	    	

</div>
</body>
</html>

<script type="text/javascript">
	$(document).ready(function(){		
		var old_type = "{{old('type')}}";
		if (old_type!=""){
			setType(old_type, true);	
		}
		
		$( "input[name=awb]" ).focus();		
		$("select[name=type]").change(function(){
			var data = $(this).val();
			console.log(data);
			setType(data, false);
		});
	})

	function setType(type, isPopulate){
		if (type=="1"){
			$(".deliver").show();
			$(".problem").hide();		
			if (!isPopulate){
				$("input[name=penerima]").attr('required',true);
				$("input[name=status]").attr('required',true);
				$("input[name=jam]").attr('required',true);
				$("input[name=menit]").attr('required',true);
			}
		}else if (type=="0"){			
			$(".deliver").hide();
			$(".problem").show();
			if (!isPopulate){
				$("select[name=description_problem]").attr('required',true);			
			}
			
		}
		if (!isPopulate){
			$("select[name=description_problem]").prop("selectedIndex", 0);
			$("input[name=penerima]").val("");
			$("input[name=status]").val("");
			$("input[name=jam]").val("");
			$("input[name=menit]").val("");
		}
	}
</script>