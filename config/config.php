<?php	    
	$data = array();
	if (env('APP_ENV')=="production"){	
		$data = [			
			'url' => "http://inventory.popbox.asia",
			"supervisor" => "spv"
		];		
	}else if (env('APP_ENV')=="staging"){
       $data = [			
			'url' => "http://inventorydev.popbox.asia",
			"supervisor" => "spv"
		];		
	}else{
		 $data = [			
			'url' => "http://express.dev",
			"supervisor" => "spv"
		];
		
	}
return $data;
?>
