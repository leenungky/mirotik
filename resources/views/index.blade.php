<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     @include('head')
</head>
<body>
	<?php use App\Http\Helpers\Helpdesk; ?> 


	<script type="text/javascript">
		$( document ).ready(function() {
		    $("#always-top-close").on( "click", function() {
		    	 $('#flash-popup').css('display','none');	
		    });

		    $(".items").on( "click", function() {
		    	console.log('a');
		    });		    
		});
	</script>
	</body>   	

</html>