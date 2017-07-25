<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use PHPExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Helpers;
use Illuminate\Support\Facades\Input;
use Redirect;
use DNS2D;
use \PHPExcel_IOFactory, \PHPExcel_Style_Fill, \PHPExcel_Cell, \PHPExcel_Cell_DataType, \SiteHelpers;

class DashboardController extends Controller {
    
    public function __construct(Request $req){
    }

	public function index(){
		$to = date('Y-m-d', strtotime('last Sunday'));       
		$from = date("Y-m-d", strtotime('-7 days', strtotime($to)));	
		$last_send = date("Y-m-d", strtotime('-2 days', strtotime($to))); 	

		$data["db_delivery"] = DB::table("inventory_sum_delivery")			
			->where("date_from", ">=", $from)
			->where("date_to", "<=", $to)
			->first();
		$data["db_merchant"] = DB::table("inventory_sum_merchant")
			->select("merchant", "total")
			->where("date_from", ">=", $from)
			->where("date_to", "<=", $to)
			->get();

		$data["db_courier"] = DB::table("inventory_sum_courier")
			->select("inventory_courier.name", "inventory_sum_courier.total")
			->leftJoin("inventory_courier", "inventory_courier.id","=","inventory_sum_courier.courier_id")
			->where("date_from", ">=", $from)
			->where("date_to", "<=", $to)
			->get();	
		$data["from"] 	= $from;
		$data["to"] 	= trim($to);
		$data["last_send"] = $last_send;
		$data["type"] 	= "dashboard";
        return view('dashboard', $data);
    }    
}
    