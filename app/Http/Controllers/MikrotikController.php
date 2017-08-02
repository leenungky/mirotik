<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\User;
use Socialize;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Redirect; 
use Illuminate\Support\Facades\Input;
use App\Lib\SiteHelpers;
use App\Lib\RouterosApi;
use \DB;

class MikrotikController extends Controller {

	
	protected $layout = "layouts.main";
	private $output;
	private $connect;
	public function __construct(Request $req) {
		$this->data["type"]= "Role";      
		$this->data["req"] = $req;
		$this->api = new RouterosApi();
		$this->api->debug = false;
		$this->api->port = 8729;
		$this->api->ssl = true;
		$this->api->timeout = 30;
		$this->connect = array("host" => "180.250.113.42", "user" => "nungky", "password" => "123");
	} 

	// public function getSsh(){
	// 	// $d = \SSH::into('production')->run([
	// 	//     'ls',
	// 	// ]);

	// 	$ssh_response = \SSH::run("user", function($line)
 //    {
 //        $this->output = $line.PHP_EOL."<br/>";
 //    });

    
	// 	print_r( $this->output);
	// 	die("==");
	// }

	public function getAdd(){
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$id=$this->api->comm("/tool/user-manager/user/add",Array( 
				"customer" => "admin",
				"username" => "nungky123",				
				"password" => "11",
				"shared-users" => "1",
			));
		}
		
	}

	public function getUpdate($id){		
		$req = $this->data["req"];
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$response = $this->api->comm("/tool/user-manager/user/set",array(
			    ".id"               => $id,
			    "username"          => "asep",
			    "password"          => "asep123"
			));				
			$this->api->disconnect(); 
			echo "<pre>";
			print_r($response);		
		}
	}

	public function getDelete($id){
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$remove=$this->api->comm("/tool/user-manager/user/remove",Array( 				
			 	 ".id" => $id,
			));			
			$this->api->disconnect(); 
		}
	}

	public function getList(){	
		if ($this->api->connect($this->connect["host"], 
			$this->connect["user"], 
			$this->connect["password"])) {
			$print=$this->api->comm("/tool/user-manager/user/print");						
			$this->api->disconnect(); 
			echo "<pre>";
			print_r($print);
			echo "<br/>";
		}	
		
	}

	
}