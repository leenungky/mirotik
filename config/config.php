<?php	    
	$data = array();
	if (env('APP_ENV')=="production"){	
		$data = [			
			'url' => "http://localhost",
			"supervisor" => "spv",
			"front_office" => "fo",
		];		
	}else if (env('APP_ENV')=="staging"){
       $data = [			
			'url' => "http://localhost",
			"supervisor" => "spv",
			"front_office" => "fo",
		];		
	}else{
		 $data = [			
			'url' => "http://localhost",
			"supervisor" => "spv",
			"front_office" => "fo",
		];
		
	}
return $data;
?>
