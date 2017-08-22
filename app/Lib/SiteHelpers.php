<?php
namespace App\Lib;	
use Illuminate\Support\Facades\DB;	

class SiteHelpers
{
		public static function generateRandomString($length = 5) {
			  $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
			  $randomString = '';
			  for ($i = 0; $i < $length; $i++) {
			    $randomString .= $characters[rand(0, strlen($characters) - 1)];
			  }			  
			  return $randomString;
		}

		public static function alert( $task , $message)	{
			if($task =='error') {
				$alert ='
				<div class="alert alert-danger  fade in block-inner">
					<button data-dismiss="alert" class="close" type="button"> x </button>
				<i class="icon-cancel-circle"></i> '. $message.' </div>
				';			
			} elseif ($task =='success') {
				$alert ='
				<div class="alert alert-success fade in block-inner">
					<button data-dismiss="alert" class="close" type="button"> x </button>
				<i class="icon-checkmark-circle"></i> '. $message.' </div>
				';			
			} elseif ($task =='warning') {
				$alert ='
				<div class="alert alert-warning fade in block-inner">
					<button data-dismiss="alert" class="close" type="button"> x </button>
				<i class="icon-warning"></i> '. $message.' </div>
				';			
			} else {
				$alert ='
				<div class="alert alert-info  fade in block-inner">
					<button data-dismiss="alert" class="close" type="button"> x </button>
				<i class="icon-info"></i> '. $message.' </div>
				';			
			}
			return $alert;
	
	} 		
			
}
