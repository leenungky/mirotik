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
            die("You are not user, please use other users");
        }
    }

	public function getList(){  
        // $date =  date("Y-m-d h:i:s");
        // $output = date('Y-m-d H:i A', strtotime($date));
        // echo $output;
        
        // die();
        if ($this->data["role"]!=config("config.supervisor")){
            return redirect('/customer/list');
        }
        $this->data["parameter"] = $_SERVER['QUERY_STRING'];        
		$req = $this->data["req"];      
        $filter= $req->input();     
        $data = $this->_get_index_filter($filter);                
        if (!empty($filter["from"]) || !empty($filter["to"])){
            $this->data["filter"] = $filter;    
        }else{
            $this->data["filter"]["from"] = date("Y-m-d");    
            $this->data["filter"]["to"] = date("Y-m-d");    
        }
        
        $this->data["report"] = $data->paginate(5);
        return view('report.index', $this->data);
    }

    public function getPdf(){        
        $req = $this->data["req"];
        $input = $req->input();                
        $data = $this->_get_index_filter($input);
        $this->data["report"] = $data->get();
        $pdf = \PDF::loadView('report.pdf', $this->data)->setPaper('a4', 'landscape')->setWarnings(false);        
        if (isset($input["from"]) && isset($input["to"])){
            return $pdf->download('report-'.$input["from"].' to '.$input['to'].'.pdf');
        }else{
            return $pdf->download('report-'.date("Y-m-d").'.pdf');    
        }
        
    }

	private function _get_index_filter($filter = null){
        $datadb = DB::table("report")            
            ->select(DB::raw("tb_users.*, report.action, report.created_at date, report.name, report.room, report.user_id"))
            ->join("tb_users", "tb_users.id", "=", "report.user_id", "left")            
            ->orderBy("report.id", "desc");
        if(!empty($filter["from"])){
            $datadb = $datadb->where("report.created_at", ">=" ,$filter["from"]);
        }
        if(!empty($filter["to"])){            
            $datadb = $datadb->where("report.created_at", "<=" ,$filter["to"]);
        }
        if (empty($filter["from"]) && empty($filter["to"])){
            $datadb = $datadb->where("report.created_at", "<=" ,"'".date('Y-m-d')."'");   
        }

        return $datadb;        
    }

}
    