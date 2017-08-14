<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use PHPExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Helpers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use \URL;
use \PHPExcel_IOFactory, \PHPExcel_Style_Fill, \PHPExcel_Cell, \PHPExcel_Cell_DataType, \SiteHelpers;

class RoomController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "master_room";    	
    	$this->data["req"]= $req;    	
        $this->data["role"] = strtolower($req->session()->get("role", ""));     
        if (empty($this->data["role"])) {
            die("You are not user, please login");
        }
    }

	public function getList(){  
		$req = $this->data["req"];      
        $input= $req->input();     
        $dbRoom = $this->_get_index_filter($input);        
        $this->data["input"] = $input;
        $this->data["room"] = $dbRoom->get();
        return view('room.index', $this->data);
    }

    public function getAdd(){		
		return view('room.new', $this->data);  
	}

    public function getSetcitykecamatan($id){
        $city = DB::table("tb_cities")->where("id", $id)->first();
        $role_user_kecamatan = DB::select("select kecamatan_id from city_kecamatan where city_id=".$id);
        $kecamatan_id_arr = array();
        foreach ($role_user_kecamatan as $key => $value) {
            $kecamatan_id_arr[] = $value->kecamatan_id;
        }       
        $kecamatan = DB::table("tb_rapid_tarif")
            ->whereNotIn("id", $kecamatan_id_arr)
            ->where("city", "like" , "%".$city->name."%")
            ->orderBy("kecamatan", "asc")->get();  
        $result_kecamatan = DB::table("tb_rapid_tarif")->whereIn("id", $kecamatan_id_arr)->orderBy("kecamatan", "asc")->get();        
    
        $this->data["kecamatan"] = $kecamatan;
        $this->data["result_kecamatan"] = $result_kecamatan;
        $this->data["city"] = $city;
        return view('city.setkecamatan', $this->data);  

    }

	public function getEdit($id){
		$room = DB::table("room")->where("id", $id)->first();       
		$this->data["room"] = $room;
		return view('room.edit', $this->data);  
	}

	public function postCreate(){	
		$req = $this->data["req"];
	 	$validator = Validator::make($req->all(), [                        
            'name' => 'required|unique:room'
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrInsert = $req->input();
        $arrInsert["created_at"] = date("Y-m-d h:i:s");
        unset($arrInsert["_token"]);        
        DB::table("room")->insert($arrInsert);        
        return redirect('/room/list')->with('message', "Successfull create");			
	}

     public function getCitiesfromkec(Request $req){
        $nama = $req->input("nama", "");           
        $data = DB::select("SELECT city  FROM `tb_rapid_tarif` where city like '%".$nama."%'");
        $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $data);
        return response()->json($res);    
    }
	
	public function postUpdate($id){	
		$req = $this->data["req"];
        $validator = Validator::make($req->all(), [                        
            'name' => 'required'
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrUpdate = $req->input();
        
        unset($arrUpdate["_token"]);        
        DB::table("room")->where("id", $id)->update($arrUpdate);        
        return redirect('/room/list')->with('message', "Successfull update");			
	}

    public function postAddcitykecamatan(){
        $req = $this->data["req"];
        DB::table("city_kecamatan")->where("city_id", $req->input("city_id"))->delete();       
        if ($req->input("ids_kecamatan")!=null ){
            $allInsert =array();
            foreach ($req->input("ids_kecamatan") as $key => $value) {
                $allInsert[] = array("city_id" => $req->input("city_id"), 
                    "kecamatan_id" => $value, 
                    "created_at" => date("y-m-d h:i:s"));           
            }
            DB::table("city_kecamatan")->insert($allInsert);
        }
        return Redirect::to('/cities/setcitykecamatan/'.$req->input("city_id"));
    }

	private function _get_index_filter($filter){
        $dbcust = DB::table("room");
        if (isset($filter["name"])){
            $dbcust = $dbcust->where("name", "like", "%".$filter["name"]."%");
        }        
        return $dbcust;
    }

}
    