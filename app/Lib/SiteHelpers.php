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
			
}
