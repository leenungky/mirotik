<?php	    
	$data = array();
	if (env('APP_ENV')=="production"){	
		$data = [			
			'url' => "http://inventory.popbox.asia",
		];
	}else if (env('APP_ENV')=="staging"){
       $data = [			
			'url' => "http://inventorydev.popbox.asia",
		];
	}else{
		 $data = [			
			'url' => "http://express.dev",
		];
	}
return $data;
?>
