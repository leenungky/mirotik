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
				<form action="/report/biaya" method="get">				
					<input type="hidden" name="sender_id" value="{{isset($input)? $input['sender_id'] : '' }}">				
					<div class="col-md-2">
						Pelanggan<br/>
						<input type="text" name="pelanggan" id="sender" placeholder="input autocomplete" class="form-control" value="{{isset($input)? $input['pelanggan'] : "" }}" required />
					</div>
					<div class="col-md-2">
						FROM<br/>
						<input type="text" name="from" class="form-control datepicker" placeholder="input tanggal" value="{{isset($input)? $input['from'] : "" }}" required />
					</div>
					<div class="col-md-2">
						TO<br/>
						<input type="text" name="to" class="form-control datepicker" placeholder="input tanggal" value="{{isset($input)? $input['to'] : "" }}" required />
					</div>
					<div class="col-md-1">
						<br/>
						<input type="submit" class="btn" value="find">
					</div>
				</form>
			</div>
			<div class="col-md-4">				
				<a href="javascript:void(0)" val-url="/report/biaya_excel?{{$parameter}}" class="btn exportexcel">Export to Excel</a> |
				<a href="javascript:void(0)" val-url="/report/biaya_pdf?{{$parameter}}" class="btn exportpdf">Export to PDF</a> 				
			</div>
		</div>
		<br/>
		@if (isset($transaction))
			<div class="report_transaction">
				<div class="row">	
					<div class="col-md-12">
						<table class="table">
							<thead>
								<th>No</th>								
								<th>No Resi</th>						
								<th>Tujuan</th>						
								<th>Penerima</th>
								<th>Harga Sebelum Discount</th>
								<th>Discount</th>
								<th>Harga Setelah Discount</th>
							</thead>
							<tbody>
								<?php 
									$i=0;
									$tot_discount = 0;
								?>
								@foreach($transaction as $trans)
									<?php 
										$dicount =   $trans->price - ($trans->price* ($customer->discount/100));
										$tot_discount = $tot_discount+$dicount;
									?>
									<tr>
										<td>
											{{++$i}}
										</td>
										<td>
											{{$trans->order_no}}
										</td>
										<td>
											{{$trans->kecamatan}}
										</td>
										<td>
											{{$trans->receipt_name}}
										</td>
										<td style="text-align: right;">
											Rp {{number_format($trans->price)}}
										</td>
										<td>
											{{$customer->discount}} %
										</td>
										<td style="text-align: right;">
											Rp {{number_format($dicount)}}
										</td>
									</tr>
								@endforeach
								<tr><td colspan="6">Total discount :</td><td style="text-align:right; ">Rp {{number_format($tot_discount)}}</td></tr>
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

		$(".exportexcel").click(function(){
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