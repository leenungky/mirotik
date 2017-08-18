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
        if ($this->data["role"]!="spv"){
            die("");
        }
    }

	public function getList(){  
        if ($this->data["role"]!=config("config.supervisor")){
            return redirect('/customer/list');
        }
		$req = $this->data["req"];      
        $input= $req->input();     
        $data = $this->_get_index_filter($input);        
        $this->data["input"] = $input;
        $this->data["report"] = $data->paginate(20);
        return view('report.index', $this->data);
    }

	private function _get_index_filter($filter){
        $data = DB::table("mikrotik")
            ->select(DB::raw("mikrotik.*, room.name as roomname, meetroom.name as meet_room_name, created.email as vcreate, updated.email as vupdate, 
            deleted.email as vdelete"))
            ->leftJoin(DB::raw("tb_users as created"), "created.id", "=", "mikrotik.created_by")
            ->leftJoin(DB::raw("tb_users as updated"), "updated.id", "=", "mikrotik.updated_by")
            ->leftJoin(DB::raw("tb_users as deleted"), "deleted.id", "=", "mikrotik.deleted_by")
            ->leftJoin("meetroom", "meetroom.id", "=", "mikrotik.meetroom_id")
            ->leftJoin("room", "room.id", "=", "mikrotik.room_id")
            ->orderBy("mikrotik.id", "desc");
        return $data;        
    }

}
    