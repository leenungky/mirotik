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
		<div class="row">	
			<div class="col-md-5">		
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">Tujuan Pengiriman</strong></div>        
					</div>					
				</div><br/>
				<form action="/collect/list" method="get">
					<div class="row">
						<div class="col-md-3">
	            			<p>Nama Kota</p>
						</div>
						<div class="col-md-6">
							<select class="form-control" name="city">
								@foreach ($city as $key => $value)
									@if ($value->id == $city_id)
	            						<option value="{{$value->id}}" selected>{{$value->name}}</option>
	            					@else
										<option value="{{$value->id}}">{{$value->name}}</option>	            					
	            					@endif
	            				@endforeach
							</select>            			
						</div>
						<div class="col-md-3">
	            			<input type="submit" value="find" class="btn">
						</div>
					</div>
				</form><br/>
				<div class="row">
					<table class="table">
						<thead>
							<th>	
							<input type="checkbox" name="all_trans_master" class="form-control" style="height: 20px;width:15px;" /></th>
							<th>awb</th><th>Penerima</th>
						</thead>
						<tbody class="master-trans">
							@if (isset($transaction))
								@foreach ($transaction as $key => $value)									
									<tr class="priv_master_{{$value->id}}">
										<td>
            								<input type="checkbox" name="ids_trans[]" value="{{$value->id}}" item1="{{$value->order_no}}" item2="{{$value->receipt_name}}" class="form-control" style="height: 20px;width:15px;" />
            							</td>
										<td>{{$value->order_no}}</td>
										<td>{{$value->receipt_name}}</td>
									</tr>
								@endforeach
							@endif
							<tr class="trans"><td></td><td></td><td></td></tr>	
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-2">
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">Action</strong></div>        
					</div>
				</div><br/>
				<div class="row">
					<div class="col-md-12">
						<input type="button" value="add >>" class="btn btn-addtrans" />						
					</div>
				</div><br/>
				<div class="row">
					<div class="col-md-12">						
						<input type="button" value="<< Remove" class="btn btn-removetrans" />						
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
            			<div class="status-barang">Hasil Penggabungan</strong></div>        
					</div>					
				</div><br/>
				<form action="/collect/addcollect" method="post" id="frm-save-trans">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="city" value="{{$city_id}}">
					<div class="row">
						<div class="col-md-4">
	            			<p>invoice no</p>
						</div>
						<div class="col-md-8">
							<input type="text" name="invoice" class="form-control" value="{{$invoice}}" required>            			
						</div>						
					</div>
					<br/>
					<div class="row">
						<table class="table">
							<thead>
								<th>	
								<input type="checkbox" name="all_trans_result" class="form-control" style="height: 20px;width:15px;" /></th>
								<th>awb</th><th>Penerima</th>
							</thead>
							<tbody class="result-trans">
								@if (isset($transaction_result))	
									@foreach ($transaction_result as $key => $value)									
										<tr class="priv_result_{{$value->id}}">
											<td>
	            								<input type="checkbox" name="ids_trans[]" value="{{$value->id}}" item1="{{$value->order_no}}" item2="{{$value->receipt_name}}" class="form-control" style="height: 20px;width:15px;" />
	            							</td>
											<td>{{$value->order_no}}</td>
											<td>{{$value->receipt_name}}</td>
										</tr>
									@endforeach
								@endif
								<tr class="trans"><td></td><td></td><td></td></tr>							
							</tbody>
						</table>
					</div>
				</form>			
			</div>

		</div>
	 </div>	    	
</div>
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){

		$(".btn-addtrans").click(function(){
			var selected = [];
			$('.master-trans input:checked').each(function() {
			    selected.push([$(this).attr('value'),$(this).attr('item1'),$(this).attr('item2')]);
			});

			$.each(selected, function( index, value ) {
				var strValue = "<tr class='priv_result_" + value[0] + "'><td>";
            		strValue = strValue + '<input type="checkbox" name="ids_trans[]" value="' + value[0] + '" item1="' + value[1] + '" item2="' + value[2] + '" class="form-control" style="height: 20px" />';
            		strValue = strValue + '</td>';
            		strValue = strValue + '<td>' + value[1] + '</td>';
            		strValue = strValue + '<td>' + value[2] + '</td></tr>';
				$(strValue).insertBefore( $( ".result-trans .trans" ) );
				$(".priv_master_" + value[0]).remove();				
			  	console.log( index + ": " + value[1] );
			});			
			$("input[name='all_trans_master']").attr('checked', false);
			
		});

		$(".btn-removetrans").click(function(){
			var selected = [];
			$('.result-trans input:checked').each(function() {
			    selected.push([$(this).attr('value'),$(this).attr('item1'),$(this).attr('item2')]);
			});

			$.each(selected, function( index, value ) {
				var strValue = "<tr class='priv_master_" + value[0] + "'><td>";
            		strValue = strValue + '<input type="checkbox" name="ids_trans[]" value="' + value[0] + '" item1="' + value[1] + '" item2="' + value[2] + '" class="form-control" style="height: 20px" />';
            		strValue = strValue + '</td>';
            		strValue = strValue + '<td>' + value[1] + '</td>';
            		strValue = strValue + '<td>' + value[2] + '</td></tr>';
             	console.log(strValue);    			
				$(strValue).insertBefore( $( ".master-trans .trans" ) );
				$(".priv_result_" + value[0]).remove();				
			  	console.log( index + ": " + value[1] );
			});			
			$("input[name='all_trans_result']").attr('checked', false);		
		});

		$("input[name='all_trans_master']").change(function(){
			if ($(this).is(":checked")){
				$('.master-trans :checkbox').each(function() {
		            this.checked = true;                        
		        });
			}else{
				$('.master-trans :checkbox').each(function() {
		            this.checked = false;                        
		        });
			}
		})


		$("input[name='all_trans_result']").change(function(){
			if ($(this).is(":checked")){
				$('.result-trans :checkbox').each(function() {
		            this.checked = true;                        
		        });
			}else{
				$('.result-trans :checkbox').each(function() {
		            this.checked = false;                        
		        });
			}
		})

		$(".btn-save-result").click(function(){
			$('.result-trans :checkbox').each(function() {
	            this.checked = true;                        
	        });
			$("#frm-save-trans").submit();
		});

		$('#frm-save-trans').submit(function(e) { // catch the form's submit event	
			
			return true;	
		});
	})
</script>