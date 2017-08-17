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
use DNS2D;

class CustomerController extends Controller {

	
	protected $layout = "layouts.main";
	private $output;
	private $connect;
	private $role;
	private $api;
	private $path = "";
	public function __construct(Request $req) {
		$this->data["type"]= "user_hotspot";      
		$this->data["req"] = $req;
		$this->data["role"] = strtolower($req->session()->get("role", ""));				
		$this->api = new RouterosApi();
		$this->api->debug = false;
		$this->api->port = 8729;
		$this->api->ssl = true;
		$this->api->timeout = 30;
		$this->path = "/ip/hotspot";		
		// $this->connect = array("host" => "180.250.113.42", "user" => "nungky", "password" => "123");				
		$this->connect = array("host" => "202.169.46.205", "user" => "nungky", "password" => "cabin888");		
	} 
	
	public function getAdd(){					
		if ($this->api->connect($this->connect["host"], 
			$this->connect["user"], 
			$this->connect["password"])) {						
			$this->data["profiles"] = $this->api->comm("/ip/hotspot/user/profile/print");			
			$this->api->disconnect(); 			
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
			$arr=$this->api->comm($this->path."/user/print",Array( 				
			 	 "?.id" => $id
			)); 			
			$this->data["profiles"] = $this->api->comm($this->path."/user/profile/print");						
			$this->data["usermkr"] = $arr[0];						
			// echo "<pre>";
			// print_r($this->data["usermkr"]);
			
			if (isset($arr[0]["profile"])){
				if ($arr[0]["profile"]=="room_profile"){
					$this->data["roomdb"] = DB::table("mikrotik")->where("mikrotik_id", $arr[0][".id"])->first();					
					// print_r($this->data["roomdb"]);
				}
			}
			// die();
		}		
		return view('foffice.edit', $this->data);
	}

	public function getDelete($id){
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$remove=$this->api->comm($this->path."/user/remove",Array( 				
			 	 ".id" => $id,
			));			
			$this->api->disconnect(); 
			$arrDeleted = array("deleted_by" =>\Auth::user()->id, "deleted_at" => date("Y-m-d h:i:s"));
			DB::table("mikrotik")->where("mikrotik_id", $id)->update($arrDeleted);
		}
		return redirect('/customer/list')->with('message', "Successfull delete");
	}

	public function getList(){	
		if (empty($this->data["role"])){
			return redirect('/user/logout');		
		}
		$this->data["usermkr"] = array();
		if ($this->api->connect($this->connect["host"], 
			$this->connect["user"], 
			$this->connect["password"])) {						
			$this->data["usermkr"] = $this->api->comm($this->path."/user/print");						
			$this->data["userprofile"] = $this->api->comm($this->path."/user/profile/print");						
			$this->api->disconnect(); 			
		}	
		return view('foffice.index', $this->data);
		
	}


	public function getPrint(){
		$req = $this->data["req"];
		$name = $req->input("name", ""); 
		$code = $req->input("password", ""); 		
		if (empty($code)){
			$res = "";			
		}else{		
			$res = DNS2D::getBarcodePNG($code, "QRCODE", 5,5);			
		}
		
		$res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => array("name" => $name, "password" =>$code), "qrcode" => $res);
        return response()->json($res);
	}

	public function postCreate(){
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];        
        $arrValidate = [            
            'name' => 'required',       
            'password' => 'required',
            'profile' => 'required',
        ];

        $input  = $req->input();
        if ($input["profile"]=="room_profile"){
        	$arrValidate["room"] = "required";
        	$arrValidate["from"] = "required";
        	$arrValidate["to"] = "required";
        	$arrValidate["day"] = "required";
        }
        $validator = Validator::make($req->all(), $arrValidate);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }	

        if ($input["profile"]=="room_profile"){
        	if (!$this->checkValidRoom($input)){
        		return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Room Masih digunakan");            	
        	}
        }
        
        $message = "Successfull created";
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$response=$this->api->comm($this->path."/user/add",Array( 				
				"name" => $input['name'],				
				"password" => $input['password'],
				"profile" => $input['profile']
			));						
			$this->api->disconnect(); 											
			if (isset($response["!trap"][0]["message"])){
				return redirect('/customer/add')->withInput(Input::all())->with('message', $response["!trap"][0]["message"]);
			}else{		
				$arrInsert = $input;		
				$arrInsert["created_by"] = \Auth::user()->id;
				$arrInsert["mikrotik_id"] = $response;
				$arrInsert["created_at"] = date("Y-m-d h:i:s");
				unset($arrInsert["_token"]);  
				DB::table("mikrotik")->insert($arrInsert);
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
            'name' => 'required',       
            'password' => 'required',
            'profile' => 'required'          
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }	
        $input  = $req->input();		
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$response = $this->api->comm($this->path."/user/set",array(
			    ".id"               => $id,
			    "name"          => $input["name"],
			    "password"          => $input["password"],
			    "profile"          => $input["profile"],
			));							
			$this->api->disconnect(); 		
			$arrUpdate = $input;		
			$arrUpdate["updated_by"] = \Auth::user()->id;
			$arrUpdate["updated_at"] = date("Y-m-d h:i:s");
			unset($arrUpdate["_token"]);  
			DB::table("mikrotik")->where("mikrotik_id", $id)->update($arrUpdate);
		}
		return redirect('/customer/list')->with('message', "Successfull delete");
	}

	public function checkValidRoom($input){
		$data = DB::table("mikrotik")->where("room", $input["room"])->whereNull("deleted_at")->first();
		if (isset($data)){
			return false;
		}else{
			return true;
		}
	}

		
}