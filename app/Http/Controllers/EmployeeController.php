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

class EmployeeController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "master_karyawan";    	
    	$this->data["req"]= $req;    	
    }

	public function getList(){  
		$req = $this->data["req"];      
        $input= $req->input();     
        $dbemploy = $this->_get_index_filter($input);        
        $this->data["employes"] = $dbemploy->get();
        return view('employ.index', $this->data);
    }

    public function getAdd(){		
		return view('employ.add', $this->data);  
	}

   	public function getEdit($id){
		$employ = DB::table("employee")->where("id", $id)->first();       
		$this->data["employ"] = $employ;
		return view('employ.edit', $this->data);  
	}

    public function getDelete($id){
        $employ = DB::table("employee")->where("id", $id)->delete();       
        return redirect('/employ/list')->with('message', "Successfull delete");     
    }

	public function postCreate(){	
		$req = $this->data["req"];
	 	$validator = Validator::make($req->all(), [            
            'nama' => 'required',
            'position' => 'required',
            'phone' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrInsert = $req->input();
        $arrInsert["created_at"] = date("Y-m-d h:i:s");
        unset($arrInsert["_token"]);        
        DB::table("employee")->insert($arrInsert);        
        return redirect('/employ/list')->with('message', "Successfull create");			
	}
	
	public function postUpdate($id){	
		$req = $this->data["req"];
        $validator = Validator::make($req->all(), [            
            'nama' => 'required',
            'position' => 'required',
            'phone' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrUpdate = $req->input();
        
        unset($arrUpdate["_token"]);        
        DB::table("employee")->where("id", $id)->update($arrUpdate);        
        return redirect('/employ/list')->with('message', "Successfull update");			
	}

    

	private function _get_index_filter($filter){
        $dbemploy = DB::table("employee");
        if (isset($filter["nama"])){
            $dbemploy = $dbemploy->where("nama", "like", "%".$filter["nama"]."%");
        }
        return $dbemploy;
    }

}
    