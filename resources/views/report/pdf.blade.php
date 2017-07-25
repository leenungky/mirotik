<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
 	<style type="text/css">
 		table, p{
 			font-family: "Arial, Helvetica, sans-serif";
 			font-size: 12px; 			
 		}

 		.list tr td{
 			padding: 5px; 		
 		}

 		.title{
 			background-color: #CCC; 			
 		}
 	</style>
</head>
<body >
	@if (isset($transaction))
	<table>
		<tr>
			<td width="400px">
				<img src="http://{{$_SERVER['SERVER_NAME']}}/img/logo-bks.png" width="100px">
			</td>
			<td>
				<table cellpadding="1" cellspacing="1">
					<tr><td>Branch<td><td>:</td><td>JAKARTA</td></tr>
					<tr><td>Pengirim<td><td>:</td><td>{{$customer->name}}</td></tr>					
					<tr><td>Tanggal<td><td>:</td><td>{{$input["from"]}}</td></tr>
					<tr><td>INVOICE<td><td>:</td><td>{{isset($total_parcel->invoice_id) ?  $total_parcel->invoice_id : ""}}</td></tr>
				</table>
			</td>
		</tr>
	</table>
	 <br/>
	@endif
 		@if (isset($transaction))				
			<table cellspacing="0" class="list" width="100%">
				<tr class="title">
					<td>No</td>								
					<td>No. Resi</td>
					<td>Tujuan</td>
					<td>Penerima</td>
					<td>Harga Sebelum Discount</td>
					<td>Discount</td>
					<td>Harga Setelah Discount</td>
				</tr>				
						<?php 
							$i=0;
							$tot_discount = 0;
							$tot_price = 0;
						?>
						@foreach($transaction as $trans)
							<?php 
								$dicount =   $trans->price - ($trans->price* ($customer->discount/100));
								$tot_price = $tot_price + $trans->price;
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
						<tr>
								<td colspan="2">Total Biaya</td><td colspan="5">: Rp {{number_format($tot_price)}}</td>
								</tr>
								<tr>
									<td colspan="2">Jumlah Paket</td><td colspan="5">: {{count($transaction)}}</td>
								</tr>
								<tr>
									<td colspan="2">Total Tagihan</td><td colspan="5">: Rp {{number_format($tot_discount)}}</td>
								</tr>
								<tr>
									<td colspan="2">Jatuh Tempo</td><td colspan="5">
										: {{date("Y-m-d", strtotime('+1 days', strtotime($input["from"])))}}
									</td>
								</tr>
							</tbody>
						</table>
					
		@endif
		<br/>
		<br/>
		<Table>
			<tr><td>Mohon untuk dapat melakukan pembayaran melalui rekening berikut :</td></tr>
			<tr><td><b>1. Bank BCA No. Rek. 5260 3588 22 Atas Nama PT PopBox Asia Services</b></td></tr>
			<tr><td><b>2. Bank Mandiri No. Rek. 1650 0091 2222 8 Atas Nama PT PopBox Asia Services</b></td></tr>
			<tr><td><b>3. Bank BNI No. Rek. 8088 0019 46 Atas Nama PT PopBox Asia Services</b></td></tr>		
		</table>		
		<p>Mohon melakukan konfirmasi ke finance@popbox.asia setelah melakukan teransfer dengan mengirimkan bukti transfer</p>
		<br/>
		<br/>
		<p>Silahkan menghubungi Customer Care di nomor 021-29022537 atau melalui info@popbox.asia jika mengalami kendala atau ketidaksesuaian</p>		
		<p>Untuk info lebih lanjut dapat di akses melalui https://www.popbox.asia</p>
</body>
</html>