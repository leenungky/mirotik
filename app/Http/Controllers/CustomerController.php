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
use \QrCode;

class CustomerController extends Controller {

	
	protected $layout = "layouts.main";
	private $output;
	private $connect;
	private $role;
	private $api;
	private $path = "";
	private $domain_qr = "http://cabinhotel.hotspot.com";
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
		
		$roomuse = DB::table("mikrotik")->select("room_id")->whereNull("deleted_at")->groupBy("room_id")->get();
		$arr_room_id_use = array();
		foreach ($roomuse as $key => $value) {
			$arr_room_id_use[] = $value->room_id;
		}		

		$meetroomuse = DB::table("mikrotik")->select("meetroom_id")->whereNull("deleted_at")->groupBy("room_id")->get();
		$arr_meetroom_id_use = array();
		foreach ($meetroomuse as $key => $value) {
			$arr_meetroom_id_use[] = $value->meetroom_id;
		} 

		$room = DB::table("room")->get();
		$meetroom = DB::table("meetroom")->get();

		$this->data["room"] = $room;
		$this->data["meetroom"] = $meetroom;
		$this->data["arr_room_id_use"] = $arr_room_id_use;
		$this->data["arr_meetroom_id_use"] = $arr_meetroom_id_use;
		
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
			$this->api->disconnect(); 			
			// echo "<pre>";
			// print_r($this->data["usermkr"]);
			
			if (isset($arr[0]["profile"])){
				$this->data["roomdb"] = DB::table("mikrotik")->where("mikrotik_id", $arr[0][".id"])->first();									
			}
			// die();
			$roomuse = DB::table("mikrotik")->select("room_id")->whereNull("deleted_at")->groupBy("room_id")->get();
			$arr_room_id_use = array();
			foreach ($roomuse as $key => $value) {
				$arr_room_id_use[] = $value->room_id;
			}

			$meetroomuse = DB::table("mikrotik")->select("meetroom_id")->whereNull("deleted_at")->groupBy("room_id")->get();
			$arr_meetroom_id_use = array();
			foreach ($meetroomuse as $key => $value) {
				$arr_meetroom_id_use[] = $value->meetroom_id;
			} 
			
			$room = DB::table("room")->get();
			$meetroom = DB::table("meetroom")->get();
			$this->data["room"] = $room;
			$this->data["meetroom"] = $meetroom;
			$this->data["arr_room_id_use"] = $arr_room_id_use;
			$this->data["arr_meetroom_id_use"] = $arr_meetroom_id_use;

			// echo "<pre>";
			// print_r($this->data["roomdb"]);
			// die();

			return view('foffice.edit', $this->data);
		}		
		
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
			$res = base64_encode(QrCode::format('png')->size(100)->generate($this->domain_qr."?username=".$name."&password=".$code));
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
        	$arrValidate["room_id"] = "required";        	
        	$arrValidate["day"] = "required";
        }else if ($input["profile"]=="meeting_profile"){
        	$arrValidate["meetroom_id"] = "required";        	
        }
        $validator = Validator::make($req->all(), $arrValidate);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }	

        if ($input["profile"]=="room_profile"){
        	if (!$this->checkValidRoom($input, "add")){
        		return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Roo Masih digunakan");            	
        	}
        }else if ($input["profile"]=="meeting_profile"){
        	if (!$this->checkValidMeetRoom($input, "add")){
        		return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Meeting Room Masih digunakan");            	
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
			// echo "<pre>";
			// print_r($response);
			// die();
			if (isset($response["!trap"][0]["message"])){
				return redirect('/customer/add')->withInput(Input::all())->with('error', $response["!trap"][0]["message"]);
			}else{		
				$arrInsert = $input;		
				if ($input["profile"] == "meeting_profile"){
					$input["day"] = 1;
				}
				$arrInsert["created_by"] = \Auth::user()->id;
				$arrInsert["mikrotik_id"] = $response;
				$arrInsert["from"] = date("Y-m-d");
        		$arrInsert["to"] =  date('Y-m-d', strtotime($arrInsert["from"] . ' + '.($input["day"]-1).' days'));
				$arrInsert["created_at"] = date("Y-m-d h:i:s");
				unset($arrInsert["_token"]);  
				DB::table("mikrotik")->insert($arrInsert);
			}
		}		
		return redirect('/customer/list')->with('message', "Successfull create");
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
        if ($input["profile"]=="room_profile"){
        	if (!$this->checkValidRoom($input, "edit", $id)){        		
        		return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Room Masih digunakan");            	
        	}
        }else if ($input["profile"]=="meeting_profile"){
        	if (!$this->checkValidMeetRoom($input, "edit", $id)){
        		return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Meeting Room Masih digunakan");            	
        	}
        }        
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
			$mikrotikdb = DB::table("mikrotik")->where("mikrotik_id", $id)->first();
			$arrUpdate = $input;		
			$arrUpdate["to"] =  date('Y-m-d', strtotime($mikrotikdb->from . ' + '.($input["day"]-1).' days'));
			$arrUpdate["updated_by"] = \Auth::user()->id;
			$arrUpdate["updated_at"] = date("Y-m-d h:i:s");
			unset($arrUpdate["_token"]);  
			DB::table("mikrotik")->where("mikrotik_id", $id)->update($arrUpdate);
		}
		return redirect('/customer/list')->with('message', "Successfull Update");
	}

	public function checkValidRoom($input,$action, $id = null){
		if ($action == "add"){
			$data = DB::table("mikrotik")->where("room_id", $input["room_id"])->whereNull("deleted_at")->first();
			if (isset($data)){
				return false;
			}else{
				return true;
			}
		}elseif ($action == "edit"){
			$data = DB::table("mikrotik")->where("mikrotik_id", $id)->whereNull("deleted_at")->first();
			if ($data->room_id == $input["room_id"]){				
				return true;
			}else{				
				$data = DB::table("mikrotik")->where("room_id", $input["room_id"])->whereNull("deleted_at")->first();
				if (isset($data)){
					return false;
				}else{
					return true;
				}
			}
			
		}
	}

	public function checkValidMeetRoom($input, $action, $id = null){		
		if ($action == "add"){
			$data = DB::table("mikrotik")->where("meetroom_id", $input["meetroom_id"])->whereNull("deleted_at")->first();			
			if (isset($data)){
				return false;
			}else{
				return true;
			}
		}elseif ($action == "edit"){			
			$data = DB::table("mikrotik")->where("mikrotik_id", $id)->whereNull("deleted_at")->first();
			if ($data->meetroom_id == $input["meetroom_id"]){				
				return true;
			}else{				
				$data = DB::table("mikrotik")->where("meetroom_id", $input["meetroom_id"])->whereNull("deleted_at")->first();
				if (isset($data)){
					return false;
				}else{
					return true;
				}
			}
			
		}
		
	}

		
}