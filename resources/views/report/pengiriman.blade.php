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
			<div class="col-md-8">
				<form action="/report/pengiriman" method="get">				
					<input type="hidden" name="sender_id" value="{{isset($input['sender_id'])? $input['sender_id'] : '' }}">				
					<div class="col-md-2">
						Pelanggan<br/>
						<input type="text" name="customer" id="sender" placeholder="input autocomplete" class="form-control" value="{{isset($input['customer'])? $input['customer'] : "" }}" required />
					</div>
					<div class="col-md-2">
						FROM<br/>
						<input type="text" name="from" class="form-control datepicker" placeholder="input tanggal" value="{{isset($input['from'])? $input['from'] : "" }}" required />
					</div>
					<div class="col-md-2">
						TO<br/>
						<input type="text" name="to" class="form-control datepicker" placeholder="input tanggal" value="{{isset($input['to'])? $input['to'] : "" }}" required />
					</div>
					<div class="col-md-1">
						<br/>
						<input type="submit" class="btn" value="find">
					</div>
				</form>
			</div>
			<div class="col-md-4">				
				<a href="/report/pengiriman_excel?{{$parameter}}" class="btn">Export to Excel</a>
				
			</div>
		</div>
		<br/>
		@if (isset($transaction))
			<div class="report_transaction">
				<div class="row">	
			<div class="col-md-12">
				<table class="table table-transaction">
					<thead>						

						<th width="120px">Pelanggan</th>		
						<th width="120px">Reseller</th>
						<th width="120px">Penerima</th>		
			    		<th width="100px">AWB</th>
			    		<th width="120px">City</th>
			    		<th width="120px">Kecamatan</th>			    		
			    		<th width="120px">Address</th>
			    		<th width="100px">Phone</th>
						<th width="90px">Weight</th>						
			    		<th width="120px">Harga</th>						
			    		<th width="110px">Status</th>	
			    		<th width="120px">Keterangan</th>	
			    		<th width="120px">Created At</th>																		
					</thead>
					<tbody>
						@foreach ($transaction as $key => $value)
							<?php
								$type = "on proses";
								$desc_type = "";
								if ($value->type=="0"){
									$type = "Bermasalah";
									$desc_type = $value->description_problem;
								}else if ($value->type=="1"){
									$type = "Di terima";
									$desc_type = $value->penerima." (".$value->status.")";
								}								
							?>
							<tr>
								<td>{{$value->name}}</td>
								<td>{{$value->reseller}}</td>
								<td>{{$value->receipt_name}}</td>
								<td>{{$value->order_no}}</td>
								<td>{{$value->city}}</td>
								<td>{{$value->kecamatan}}</td>
								<td>{{$value->address}}</td>
								<td>{{$value->phone}}</td>
								<td>{{$value->weight}}</td>
								<td style="text-align: right;width: 100px">Rp {{number_format($value->price)}}</td>
								<td>
									{{$type}}
								</td>
								<td>
									{{$desc_type}}
								</td>
								<td>{{$value->created_at}}</td>															
							</tr>																							
						@endforeach					
					</tbody>
				</table>
			</div>
		</div>
			</div>
		@endif
	 </div>	    	
</div>
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){		
		$(".exportpdf").click(function(){
			var from = $("input[name=from]").val();
			var to = $("input[name=to]").val();
			if (from!=""){
				if (from==to){
					var url = $(this).attr("val-url");
					location.href= url;
				}else{
					alert("Tanggal from and tanggal to harus sama");
				}
			}
			
		})
	})
</script>