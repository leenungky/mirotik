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

class ReportController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "REPORT";    	
    	$this->data["req"]= $req;    	
        $this->data["role"] = strtolower($req->session()->get("role", ""));     
        if (empty($this->data["role"])) {
            die("You are not user, please login");
        }
        if ($this->data["role"]!="administrator"){
            die("");
        }
    }

	public function getList(){  
		$req = $this->data["req"];      
        $input= $req->input();     
        $sql = $this->_get_index_filter($input);        
        $this->data["input"] = $input;
        $this->data["report"] = DB::select($sql);
        return view('report.index', $this->data);
    }

	private function _get_index_filter($filter){
        $sql = "SELECT mikrotik.*, room.name as roomname, meetroom.name as meet_room_name, created.email as vcreate, updated.email as vupdate, 
            deleted.email as vdelete FROM `mikrotik`
                left join tb_users as created on created.id=mikrotik.created_by
                left join tb_users as updated on updated.id=mikrotik.updated_by
                left join tb_users as deleted on deleted.id=mikrotik.deleted_by
                left join meetroom on meetroom.id=mikrotik.meetroom_id
                left join room on room.id=mikrotik.room_id";     
        return $sql." Order By mikrotik.id asc ";
    }

}
    