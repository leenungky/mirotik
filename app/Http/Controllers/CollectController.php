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

class CollectController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "Gabungan"; 
    	$this->data["req"]= $req;    	
    }

	public function getList(){        
		$req = $this->data["req"];
        $input = $req->input();
        $this->data["city_id"] = "";
        $this->data["invoice"] = "";
        if (isset($input["city"])){
            $role_user_kecamatan = DB::select("select kecamatan_id from city_kecamatan where city_id=".$input["city"]);
            $kecamatan_id_arr = array();
            foreach ($role_user_kecamatan as $key => $value) {
                $kecamatan_id_arr[] = $value->kecamatan_id;
            } 
            $transaction = DB::table("inventory_transaction")->whereIn("kecamatan_id", $kecamatan_id_arr)
                ->where("iscollect", 0)
                ->get();
            $transaction_result = DB::table("inventory_transaction")->whereIn("kecamatan_id", $kecamatan_id_arr)
                ->where("iscollect", 1)
                ->get();
            $transaction_result_invoice = DB::table("inventory_transaction")
                ->select("invoice_collect")
                ->whereIn("kecamatan_id", $kecamatan_id_arr)
                ->where("iscollect", 1)
                ->groupBy("invoice_collect")
                ->first();            
            $this->data["transaction"] = $transaction;
            $this->data["transaction_result"] = $transaction_result;
            $this->data["invoice"] = isset($transaction_result_invoice->invoice_collect) ? $transaction_result_invoice->invoice_collect : "";
            $this->data["city_id"] = $input["city"];             
        }
        $input= $req->input();
        $city = DB::table("tb_cities")->get();
        $this->data["city"] = $city;
        return view('collect.index', $this->data);
    }

    public function postAddcollect(){
        $req = $this->data["req"];   
        $input = $req->input();
        $allUpdate = array("iscollect" => 0, "city_id" => 0, "invoice_collect" => "");
        DB::table("inventory_transaction")->where("city_id", $input["city"])->update($allUpdate);
        if (isset($input["ids_trans"])){            
            $allUpdate = array("iscollect" => 1, "city_id" => $input["city"], "invoice_collect" => $input["invoice"]);
            DB::table("inventory_transaction")->whereIn("id", $input["ids_trans"])->update($allUpdate);
        }
        return Redirect::to('/collect/list?city='.$input["city"]);
    }

}
    