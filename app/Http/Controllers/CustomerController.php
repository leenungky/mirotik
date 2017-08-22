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
	private $domain_qr = "http://cabinhotel.hotspot.com/login";
	private $fo_profiles = array("room_profile", "meeting_profile_suzuka", "meeting_profile_monza", "meeting_profile_monaco", "meeting_profile_sepang");
	public function __construct(Request $req) {
		$this->data["type"]= "user_hotspot";      
		$this->data["req"] = $req;
		$this->data["role"] = strtolower($req->session()->get("role", ""));				
		if (empty($this->data["role"])){
			die();
		}
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
		$room_in_use = DB::table("mikrotik")->select("room")->whereNull("deleted_at")->groupBy("room")->get();;				
		$room_in_use = json_decode(json_encode($room_in_use), True);		
		
		$room = DB::table("room")->select("name")->whereNotIn("name", $room_in_use);
		if ($this->data["role"]==config("config.front_office")){
			$room = $room->where("ishidden",0);
		}
		$room = $room->get();
		$meetroom = DB::table("meetroom")->select("name")->whereNotIn("name", $room_in_use)->get();
		$this->data["room"] = $room;
		$this->data["meetroom"] = $meetroom;
		return view('foffice.new', $this->data);
	}

	public function getAddmanagement(){									
		if ($this->data["role"]!=config("config.supervisor")){
			return redirect('/customer/list');
		}
		return view('foffice.management', $this->data);
	}


	public function getEdit($id){		
		$message = $this->getMessageLock($id, "mengedit");
		if(!empty($message) ){
			return Redirect::to(URL::previous())->withInput(Input::all())->with("message", $message);            	
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
			
			if (isset($arr[0]["profile"])){
				$this->data["mikrotik"] = DB::table("mikrotik")->where("mikrotik_id", $arr[0][".id"])->first();									
			}
			
			$roomedit = "";
			if (isset($this->data["mikrotik"])){
				$roomedit = $this->data["mikrotik"]->room;
			}
			
			$room_in_use = DB::table("mikrotik")->select("room")->whereNull("deleted_at")->where("room", "<>", $roomedit)->groupBy("room")->get();		
			$room_in_use = json_decode(json_encode($room_in_use), True);					

			$room = DB::table("room")->select("name")->whereNotIn("name", $room_in_use);
			if ($this->data["role"]==config("config.front_office")){
				$room = $room->where("ishidden",0);
			}
			$room = $room->get();			
			$meetroom = DB::table("meetroom")->select("name")->whereNotIn("name", $room_in_use)->get();						
			$this->data["room"] = $room;
			$this->data["meetroom"] = $meetroom;
			return view('foffice.edit', $this->data);
		}		
		
	}

	public function getDelete($id){						
		$message = $this->getMessageLock($id, "menghapus");
		if(!empty($message) ){
			return Redirect::to(URL::previous())->withInput(Input::all())->with("message", $message);            	
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
			$idArray = array();
			foreach ($this->data["usermkr"] as $key => $value) {
				$idArray[] = $value[".id"];
			}

			$mikrotikDB = DB::table("mikrotik")->whereIn("mikrotik_id",$idArray)->get();
			$mikrotikArray = array();
			foreach ($mikrotikDB as $key => $value) {
				$mikrotikArray[$value->mikrotik_id] = $value->room;
			}

			$showArray = array();
			foreach ($this->data["usermkr"] as $key => $value) {
				if ($this->data["role"]==config("config.front_office")){
					if (isset($value["profile"]) ){						
						if (in_array($value["profile"], $this->fo_profiles)){
							$showArray[] = array("id" =>$value[".id"], "name"=>$value["name"], "room" =>isset($mikrotikArray[$value[".id"]]) ? $mikrotikArray[$value[".id"]] : "",  "password" =>isset($value["password"]) ? $value["password"] : "");				
						}
					} 
				}else{
					$showArray[] = array("id" =>$value[".id"], "name"=>$value["name"], "room" =>isset($mikrotikArray[$value[".id"]]) ? $mikrotikArray[$value[".id"]] : "",  "password" =>isset($value["password"]) ? $value["password"] : "");				
				}
			}	

			$req = $this->data["req"];
			$input= $req->input();        
			$array = $this->_get_index_sort($req, $input, $showArray);

			$this->data["show"] = $array;
			$this->api->disconnect(); 			
		}	
		return view('foffice.index', $this->data);
		
	}


	public function getPrint(){
		$req = $this->data["req"];
		$name = $req->input("name", ""); 
		$code = $req->input("password", ""); 				
		$res = base64_encode(QrCode::format('png')->size(110)->generate($this->domain_qr."?username=".$name."&password=".$code));
		$response = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => array("name" => $name, "password" =>$code), "qrcode" => $res);
        return response()->json($response);
	}

	public function postCreate(){
		if ($this->data["role"]!=config("config.supervisor")){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];        
        $arrValidate = [            
            'name' => 'required',                   
            "room"=> 'required', 
            "checkout"=> 'required',             
        ];

        $validator = Validator::make($req->all(), $arrValidate);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }

        $input  = $req->input();        
        if (!$this->checkValidRoom($input, "add")){
        	return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Room ".$input["room"]." Sudah digunakan");            
        }

        
        $message = "Successfull created";
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$password = SiteHelpers::generateRandomString();
			$room = DB::table("room")->where("name", $input["room"])->first();						
			$profile = "";
			if (isset($room)){
				$profile = "room_profile";
			}else{
				$meetroom = DB::table("meetroom")->where("name", $input["room"])->first();			
				if (isset($meetroom)){
					$profile = $meetroom->profile;	
				}
			}
			
			$response=$this->api->comm($this->path."/user/add",Array( 				
				"name" => $input['name'],				
				"password" => $password,
				"profile" => $profile
			));						
			$this->api->disconnect(); 													
			if (isset($response["!trap"][0]["message"])){
				return redirect('/customer/add')->withInput(Input::all())->with('error', $response["!trap"][0]["message"]);
			}else{
				$arrInsert = $input;						
				$arrInsert["password"] = $password;
				$arrInsert["created_by"] = \Auth::user()->id;
				$arrInsert["mikrotik_id"] = $response;
				$arrInsert["checkin"] = date("Y-m-d");        		
				$arrInsert["created_at"] = date("Y-m-d h:i:s");
				unset($arrInsert["_token"]);  
				DB::table("mikrotik")->insert($arrInsert);
			}
		}		
		return redirect('/customer/list')->with('message', "Successfull create");
	}

	public function postCreatemanagement(){
		if ($this->data["role"]!=config("config.supervisor")){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];        
        $arrValidate = [            
            'name' => 'required',
        ];

        $validator = Validator::make($req->all(), $arrValidate);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }

        $input  = $req->input();        
        $message = "Successfull created";
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$password = SiteHelpers::generateRandomString();			
			$profile = "management_profile";
			
			
			$response=$this->api->comm($this->path."/user/add",Array( 				
				"name" => $input['name'],				
				"password" => $password,
				"profile" => $profile
			));						
			$this->api->disconnect(); 													
			if (isset($response["!trap"][0]["message"])){
				return redirect('/customer/management')->withInput(Input::all())->with('error', $response["!trap"][0]["message"]);
			}else{		
				$arrInsert = $input;						
				$arrInsert["room"] = "management";
				$arrInsert["password"] = $password;
				$arrInsert["created_by"] = \Auth::user()->id;
				$arrInsert["mikrotik_id"] = $response;
				$arrInsert["checkin"] = date("Y-m-d");        		
				$arrInsert["created_at"] = date("Y-m-d h:i:s");
				unset($arrInsert["_token"]);  
				DB::table("mikrotik")->insert($arrInsert);
			}
		}		
		return redirect('/customer/list')->with('message', $message);
	}

	public function postUpdate($id){				
		$req = $this->data["req"];
		$arrValidate = [            
            'name' => 'required',                   
            "room"=> 'required', 
            "checkout"=> 'required',             
        ];        
        $validator = Validator::make($req->all(),$arrValidate);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }	

        $input  = $req->input();		        

        if (!$this->checkValidRoom($input, "edit", $id)){
        	return Redirect::to(URL::previous())->withInput(Input::all())->withErrors("Room ".$input["room"]." Sudah digunakan");            
        }
		if ($this->api->connect($this->connect["host"], 
				$this->connect["user"], 
				$this->connect["password"])) {
			$password = SiteHelpers::generateRandomString();
			$room = DB::table("room")->where("name", $input["room"])->first();			
			$profile = "";
			if (isset($room)){
				$profile = "room_profile";	
			}else{
				$meetroom = DB::table("meetroom")->where("name", $input["room"])->first();			
				if (isset($meetroom)){
					$profile = $meetroom->profile;	
				}
				
			}

			$response = $this->api->comm($this->path."/user/set",array(
			    ".id"               => $id,
			    "name"          => $input["name"],
			    "password"          => $password,
			    "profile"          => $profile,
			));							
			$this->api->disconnect(); 		
			$mikrotikdb = DB::table("mikrotik")->where("mikrotik_id", $id)->first();
			$arrUpdate = $input;								
			$arrUpdate["updated_by"] = \Auth::user()->id;
			$arrUpdate["updated_at"] = date("Y-m-d h:i:s");
			unset($arrUpdate["_token"]);  
			if (isset($mikrotikdb)){				
				DB::table("mikrotik")->where("mikrotik_id", $id)->update($arrUpdate);
			}else{
				$arrUpdate["mikrotik_id"] = $id;
				DB::table("mikrotik")->insert($arrUpdate);
			}
		
		}	
		return redirect('/customer/list')->with('message', "Successfull Update");
	}


	 private function _get_index_sort($req, $input, $showArray){ 
	 	$array = collect($showArray)->sortBy('room')->toArray();                       		
        if (isset($input["sort"])){
            if (empty($input["order_by"])){
                $order_by = "asc";       
            }else{
                $order_by = $input["order_by"];
            }
            $this->data["order_by"] = $order_by; 
            $this->data["sort"] = $input["sort"];

            if ($input["sort"]=="room"){
                if ($order_by == "asc"){
                    $this->data["arrow_nama"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                	$array = collect($showArray)->sortBy('room')->reverse()->toArray();			
                    $this->data["arrow_nama"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                     
            }
            
        }   
        return $array;                    
    }
	

	public function checkValidRoom($input,$action, $id = null){
		if ($action == "add"){
			$data = DB::table("mikrotik")->where("room", $input["room"])->whereNull("deleted_at")->first();
			if (isset($data)){
				return false;
			}else{
				return true;
			}
		}elseif ($action == "edit"){
			$data = DB::table("mikrotik")->where("mikrotik_id", $id)->whereNull("deleted_at")->first();
			if (isset($data->room)){
				if ($data->room == $input["room"]){				
					return true;
				}else{				
					$data = DB::table("mikrotik")->where("room", $input["room"])->whereNull("deleted_at")->first();
					if (isset($data)){
						return false;
					}else{
						return true;
					}
				}	
			}else{
				return true;
			}
		}
	}

	public function getMessageLock($id, $action){
		$msg = "";
		$mikrotik = DB::table("mikrotik")->select("room")->where("mikrotik_id", $id)->first();		
		if (isset($mikrotik)){
			$room = DB::table("room")->where("name", $mikrotik->room)->first();
			if (isset($room)){
				if ($room->islock==1){								
					$msg =  "Tidak dapat ".$action." user untuk Room ".$room->name." sedang di lock oleh supevisor";					
				}
			}
		}
		return $msg;
	}
		
}