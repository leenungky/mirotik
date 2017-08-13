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
use \URL;

class CustomerController extends Controller {

	
	protected $layout = "layouts.main";
	private $output;
	private $connect;
	private $role;
	public function __construct(Request $req) {
		$this->data["type"]= "User_Mikrotik";      
		$this->data["req"] = $req;
		$this->data["role"] = strtolower($req->session()->get("role", ""));
		$this->api = new RouterosApi();
		$this->api->debug = false;
		$this->api->port = 8729;
		$this->api->ssl = true;
		$this->api->timeout = 30;
		$this->connect = array("host" => "180.250.113.42", "user" => "nungky", "password" => "123");
		// $this->connect = array("host" => "202.169.46.205", "user" => "nungky", "password" => "cabin888");
		
	} 
	
	public function getAdd(){			
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}			
		return view('foffice.new', $this->data);
	}

	public function getEdit($id){				
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$arr=$this->api->comm("/tool/user-manager/user/print",Array( 				
			 	 "?.id" => $id
			)); 			
			$this->data["usermkr"] = $arr[0];			
		}		
		return view('foffice.edit', $this->data);
	}

	public function postCreate(){
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];
        $validator = Validator::make($req->all(), [            
            'username' => 'required',       
            'password' => 'required'          
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }	
        $input  = $req->input();
        $message = "Successfull update";
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$response=$this->api->comm("/tool/user-manager/user/add",Array( 
				"customer" => "admin",
				"username" => $input['username'],				
				"password" => $input['password'],
				"shared-users" => "1",
			));
			$this->api->disconnect(); 		
			if (isset($response["!trap"][0]["message"])){
				return redirect('/customer/add')->withInput(Input::all())->with('message', $response["!trap"][0]["message"]);
			}			
		}		
		return redirect('/customer/list')->with('message', "Successfull update");
	}

	public function postUpdate($id){		
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];
        $validator = Validator::make($req->all(), [            
            'username' => 'required',       
            'password' => 'required'          
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }	
        $input  = $req->input();		
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$response = $this->api->comm("/tool/user-manager/user/set",array(
			    ".id"               => $id,
			    "username"          => $input["username"],
			    "password"          => $input["password"]
			));				
			$this->api->disconnect(); 		
		}
		return redirect('/customer/list')->with('message', "Successfull delete");
	}

	public function getDelete($id){
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$remove=$this->api->comm("/tool/user-manager/user/remove",Array( 				
			 	 ".id" => $id,
			));			
			$this->api->disconnect(); 
		}
		return redirect('/customer/list')->with('message', "Successfull delete");
	}

	public function getList(){	
		$this->data["usermkr"] = array();
		if ($this->api->connect($this->connect["host"], 
			$this->connect["user"], 
			$this->connect["password"])) {
			$this->data["usermkr"] = $this->api->comm("/tool/user-manager/user/print");						
			$this->api->disconnect(); 			
		}	
		return view('foffice.index', $this->data);
		
	}
	
}