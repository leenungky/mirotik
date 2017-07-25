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
<body>
    <?php use App\Http\Helpers\Helpdesk; ?>

 
 <div id="contents">
    <div class="container container-fluid">            	
    	<div class="row">
    		<div class="col-md-12">
    			<div id="printableArea" style="display: none;">
				    <div><h3>Print me</h3></div>
				    <div style="width: 500px">
				    	<?php
				    	echo '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG("123456789", "QRCODE", 10,10) . '" alt="barcode"   />';
				    	?>
				    </div>
				    <div><h3>ular</h3></div>
				</div>
    		</div>
    		<input type="text" value=""/>
    		<input type="button" onclick="printDiv('printableArea')" value="print a div!" />
    	</div>
    </div>   
</div>
</body>
</html>

<script type="text/javascript">
	function printDiv(divName) {		
	    var printContents = document.getElementById(divName).innerHTML;
	    var originalContents = document.body.innerHTML;
	    document.body.innerHTML = printContents;
	    window.print();
	    document.body.innerHTML = originalContents;	 
	}
</script>

