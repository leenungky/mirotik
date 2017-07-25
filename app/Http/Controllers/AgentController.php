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

class AgentController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "master_perwakilan";    	
    	$this->data["req"]= $req;    	
    }

	public function getList(){  
		$req = $this->data["req"];      
        $input= $req->input();     
        $dbagent = $this->_get_index_filter($input);
        $this->data["agent"] = $dbagent->get();
        return view('agent.index', $this->data);
    }

    public function getAdd(){
        $cities = DB::table("tb_cities")->get();
        $this->data["cities"] = $cities;		
		return view('agent.new', $this->data);  
	}

	public function getEdit($id){
		$agent = DB::table("agent")->where("id", $id)->first();
        $cities = DB::table("tb_cities")->get();
        $this->data["cities"] = $cities;    
		$this->data["agent"] = $agent;
		return view('agent.edit', $this->data); 
	}

	public function postCreate(){	
		$req = $this->data["req"];
	 	$validator = Validator::make($req->all(), [            
            'name' => 'required',
            'phone' => 'required|unique:agent',
            'city' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrInsert = $req->input();
        $arrInsert["created_at"] = date("Y-m-d h:i:s");
        $arrInsert["city_id"] = $arrInsert["city"];
        unset($arrInsert["_token"]);
        unset($arrInsert["city"]);        
        // print_r($arrInsert);
        // die();
        DB::table("agent")->insert($arrInsert);        
        return redirect('/agent/list')->with('message', "Successfull create");			
	}

    public function getDelete(Request $req, $id){       
        $user = DB::table("agent")->where("id" , $id)->delete();
        return redirect('/agent/list')->with('message', "Successfull delete");
    }

    	
	public function postUpdate($id){	
        $req = $this->data["req"];
		$validator = Validator::make($req->all(), [            
            'name' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrUpdate = $req->input();
        $arrUpdate["city_id"] = $arrUpdate["city"];
        unset($arrUpdate["city"]);   
        unset($arrUpdate["_token"]);        
        DB::table("agent")->where("id", $id)->update($arrUpdate);        
        return redirect('/agent/list')->with('message', "Successfull update");			
	}

	private function _get_index_filter($filter){
        $dbcust = DB::table("agent")
            ->select("agent.id","agent.name", "agent.phone", "agent.address", "tb_cities.name as kota")
            ->join("tb_cities","agent.city_id","=","tb_cities.id", "left");
        if (isset($filter["name"])){
            $dbcust = $dbcust->where("agent.name", "like", "%".$filter["name"]."%");
        }        
        return $dbcust;
    }

}
    