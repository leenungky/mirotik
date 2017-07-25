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
			<div class="col-md-5">
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">List Master Kecamatan {{ucfirst($city->name)}}</strong></div>        
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
            			<table class="table">
            				<thead>
            					<th>
            						<input type="checkbox" name="all_kecamatan_master" class="form-control" style="height: 20px;width:15px;" />
            					</th>
            					<th>
            						City
            					</th>
            					<th>
            						Kecamatan
            					</th>
            				</thead>
            				<tbody class="master-kecamatan">
            					@foreach ($kecamatan as $key => $value)            						
            					<tr class="priv_master_{{$value->id}}">
            						<td>
            							<input type="checkbox" name="ids_kecamatan[]" value="{{$value->id}}" item1="{{$value->city}}" item2="{{$value->kecamatan}}" class="form-control" style="height: 20px;width:15px;" />
            						</td>
            						<td>{{$value->city}}</td>
            						<td>{{$value->kecamatan}}</td>
            					</tr>
            					@endforeach
            				<tr class="kecamatan"><td></td><td></td><td></td></tr>	
            				</tbody>
            			</table>
					</div>
				</div>				
			</div>
			<div class="col-md-2 center">
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">Action</strong></div>        
					</div>
				</div><br/><br/><br/>
				<div class="row">
					<div class="col-md-12">
						<input type="button" value="add >>" class="btn btn-addkecamatan" />						
					</div>
				</div><br/>
				<div class="row">
					<div class="col-md-12">						
						<input type="button" value="<< Remove" class="btn btn-removekecamatan" />						
					</div>
				</div><br/>
				
				<div class="row">
					<div class="col-md-12">						
						<input type="button" value="Save" class="btn btn-save-result" />
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">Set {{ucfirst($city->name)}} kecamatan</strong></div>        
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<form action="/cities/addcitykecamatan" method="post" id="frm-save-kecamatan">					
							<input type="hidden" name="_token" value="{{ csrf_token() }}">					
							<input type="hidden" name="city_id" value="{{$city->id}}">
	            			<table class="table">
	            				<thead>
	            					<th>
	            						<input type="checkbox" name="all_kecamatan_result" class="form-control" style="height: 20px;width:15px;" />
	            					</th>
	            					<th>City</th><th>Kecamatan</th>
	            				</thead>
	            				<tbody class="result-kecamatan">            				
	            				@foreach ($result_kecamatan as $key => $value)            						
	            					<tr class="priv_result_{{$value->id}}">
	            						<td>
	            							<input type="checkbox" name="ids_kecamatan[]" value="{{$value->id}}" item1="{{$value->city}}" item2="{{$value->kecamatan}}" class="form-control" style="height: 20px;width:15px;" />
	            						</td>
	            						<td>{{$value->city}}</td>
	            						<td>{{$value->kecamatan}}</td>
	            					</tr>
            					@endforeach
	            				<tr class="kecamatan"><td></td><td></td><td></td></tr>
	            				</tbody>            			
	            			</table>
            			</form>
					</div>
				</div>				
			</div>
		</div>
	 </div>	    	
</div>
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
		$(".btn-addkecamatan").click(function(){
			var selected = [];
			$('.master-kecamatan input:checked').each(function() {
			    selected.push([$(this).attr('value'),$(this).attr('item1'),$(this).attr('item2')]);
			});

			$.each(selected, function( index, value ) {
				var strValue = "<tr class='priv_result_" + value[0] + "'><td>";
            		strValue = strValue + '<input type="checkbox" name="ids_kecamatan[]" value="' + value[0] + '" item1="' + value[1] + '" item2="' + value[2] + '" class="form-control" style="height: 20px" />';
            		strValue = strValue + '</td>';
            		strValue = strValue + '<td>' + value[1] + '</td>';
            		strValue = strValue + '<td>' + value[2] + '</td></tr>';
				$(strValue).insertBefore( $( ".result-kecamatan .kecamatan" ) );
				$(".priv_master_" + value[0]).remove();				
			  	console.log( index + ": " + value[1] );
			});			
			$("input[name='all_kecamatan_master']").attr('checked', false);
			
		});

		$(".btn-removekecamatan").click(function(){
			var selected = [];
			$('.result-kecamatan input:checked').each(function() {
			    selected.push([$(this).attr('value'),$(this).attr('item1'),$(this).attr('item2')]);
			});

			$.each(selected, function( index, value ) {
				var strValue = "<tr class='priv_master_" + value[0] + "'><td>";
            		strValue = strValue + '<input type="checkbox" name="ids_priviledge[]" value="' + value[0] + '" item1="' + value[1] + '" item2="' + value[2] + '" class="form-control" style="height: 20px" />';
            		strValue = strValue + '</td>';
            		strValue = strValue + '<td>' + value[1] + '</td>';
            		strValue = strValue + '<td>' + value[2] + '</td></tr>';
             	console.log(strValue);    			
				$(strValue).insertBefore( $( ".master-kecamatan .kecamatan" ) );
				$(".priv_result_" + value[0]).remove();				
			  	console.log( index + ": " + value[1] );
			});			
			$("input[name='all_kecamatan_result']").attr('checked', false);		
		});

		$("input[name='all_kecamatan_master']").change(function(){
			if ($(this).is(":checked")){
				$('.master-kecamatan :checkbox').each(function() {
		            this.checked = true;                        
		        });
			}else{
				$('.master-kecamatan :checkbox').each(function() {
		            this.checked = false;                        
		        });
			}
		})

		$("input[name='all_kecamatan_result']").change(function(){
			if ($(this).is(":checked")){
				$('.result-kecamatan :checkbox').each(function() {
		            this.checked = true;                        
		        });
			}else{
				$('.result-kecamatan :checkbox').each(function() {
		            this.checked = false;                        
		        });
			}
		})

		$(".btn-save-result").click(function(){
			$('.result-kecamatan :checkbox').each(function() {
	            this.checked = true;                        
	        });
			$("#frm-save-kecamatan").submit();
		});

	});
</script>