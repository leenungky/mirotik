<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use PHPExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Helpers;
use DNS2D;
use Illuminate\Support\Facades\Input;
use \PHPExcel_IOFactory, \PHPExcel_Style_Fill, \PHPExcel_Cell, \PHPExcel_Cell_DataType, \SiteHelpers;
use \PHPExcel_Worksheet_Drawing;

class TransactionController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "transaction";        
        $this->data["req"] = $req;   
    }

	 public function index(Request $req){
        $input = $req->input();        
        $transDB = $this->_get_index_filter($input);     
        $transDB = $this->_get_index_sort($req, $transDB, $input);     
        $transDB = $transDB->paginate(20);        
        $this->data["trans"] = $transDB;
        $this->data["filter"] = $input;
        $helpers = new Helpers();
        $this->data["helpers"] = new Helpers();                     
        return view('transaction.index', $this->data);
    }


    public function cancel(Request $req){        
        return redirect('/transaction')->with('message', "Successfull delete transaction");
    }

    public function create(Request $req){
        $input = $req->input();
        $new_parcel_id = $req->session()->get("new_parcel_id");
        $arrInsert = array(
                "order_no" => Helpers::get_rw_build_transaction(),
                "customer_total_parcel_id" => $new_parcel_id,
                "sender_id" => $input["sender_id"],
                "reseller" => $input["reseller"],
                "receipt_name" => $input["receive"],
                "kecamatan_id" => $input["city_id"],
                "phone" => $input["phone"],
                "weight" => $input["weight"],
                "address" => $input["address"],
                "price" => 0,
                "created_at" => date("Y-m-d H:i:s")
            );        
        $dbtarif = DB::table("tb_rapid_tarif")
            ->select("regular_price")
            ->where("id", $input["city_id"])
            ->first();
        if (isset($dbtarif)){
            $arrInsert["price"] = $dbtarif->regular_price * (int)$arrInsert["weight"];
        }

        DB::table("inventory_transaction")->insert($arrInsert);
        $total_parcel_db = DB::table("inventory_customer_total_parcel")->where("id", $new_parcel_id)->first();
        $total_transaction = DB::table("inventory_transaction")->where("customer_total_parcel_id", $new_parcel_id)->count();  
        if ($total_transaction >= $total_parcel_db->total){
            return redirect('/transaction')->with('message', "Successfull create new transaction");
        }
        return redirect('/transaction/new')->with('message', "Successfull create new transaction");
    }

    public function createajax(Request $req){
        $input = $req->input();
        $new_parcel_id = $req->session()->get("new_parcel_id");
        $arrInsert = array(
                "order_no" => Helpers::get_rw_build_transaction(),
                "customer_total_parcel_id" => $new_parcel_id,
                "reseller" => $input["reseller"],
                "sender_id" => $input["sender_id"],
                "receipt_name" => $input["receive"],
                "kecamatan_id" => $input["city_id"],
                "phone" => $input["phone"],
                "weight" => $input["weight"],
                "address" => $input["address"],
                "price" => 0,
                "created_at" => date("Y-m-d H:i:s")
            );
        
        $dbtarif = DB::table("tb_rapid_tarif")
            ->select("regular_price")
            ->where("id", $input["city_id"])
            ->first();        
        if (isset($dbtarif)){
            $arrInsert["price"] = $dbtarif->regular_price * (int)$arrInsert["weight"];
        }

        $kecamatandb = DB::table("tb_rapid_tarif")->where("id", $input["city_id"])->first();
        $address = $input["address"].", ".$kecamatandb->kecamatan.", ".$kecamatandb->city;
        $res = DNS2D::getBarcodePNG($arrInsert["order_no"], "QRCODE", 5,5);      
        $kecamatan = "";
        if ($kecamatandb->kecamatan)  
            $kecamatan = $kecamatandb->kecamatan;

        $id = DB::table("inventory_transaction")->insertGetId($arrInsert);        
        if (isset($id)){
            $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $arrInsert,"address" => $address, "kecamatan" => $kecamatan, "qrcode" => $res);
            return response()->json($res);        
        }
    }

    public function create_taken(Request $req){
        $input = $req->input();        
        $transactiondb = DB::table("inventory_transaction")->where("order_no", $input["awb"])->first();
        if (!isset($transactiondb)){
            return redirect('/transaction/taken')->with('message', "AWB <strong>".$input["awb"]."</strong> Not Found")->withInput();
        }
        $inv_history = DB::table("inventory_history")
            ->where("order_no", $input["awb"])
            ->orderBy("id_history","desc")
            ->first();
        if (!isset($inv_history)){
            return redirect('/transaction/taken')->with('message', "AWB <strong>".$input["awb"]."</strong> harus scan")->withInput();
        }
        if ($inv_history->status!="out"){
            return redirect('/transaction/taken')->with('message', "AWB <strong>".$input["awb"]."</strong> harus scan out")->withInput();
        }
        $taken = DB::table("inventory_taken")->where("order_no", $input["awb"])->first();        
        $dataIns = array(                
            "type" => $input["type"],
            "delivery_date" => $input['delivery_date'],
            "description_problem" => $input["description_problem"],
            "penerima" => $input["penerima"],
            "status" => $input["status"],
            "jam" => $input["jam"],
            "menit" => $input["menit"],
            "created_at" => date("Y-m-d H:i:s")
        );
        if (isset($taken)){            
            if ($taken->type=="1"){
                return redirect('/transaction/taken')->with('message', "AWB <strong>".$input["awb"]."</strong> Sudah diterima customer");   
            }else{
                $id = DB::table("inventory_taken")->where("order_no", $input["awb"])->update($dataIns);    
            }
            
        }else{
            if ($input['type']=="0") {
                $insert_history = (array)$inv_history;
                unset($insert_history["id_history"]);
                $insert_history["status"] ="in";
                $insert_history["remark"] = $input["description_problem"];
                $insert_history["last_update"] = date("Y-m-d H:i:s"); 
                DB::table("inventory_history")->insert($insert_history);
            }
            $dataIns["order_no"] = $input["awb"];
            $id = DB::table("inventory_taken")->insertGetId($dataIns);
        }
        
        if (isset($id)){
            $message = "AWB <strong>".$input["awb"]."</strong> sudah diterima";
            if ($input["type"]=="0"){
                $message = "AWB <strong>".$input["awb"]."</strong> Berhasil input dengan type bermasalah";
            }
            return redirect('/transaction/taken')->with('message', $message);    
            
        }
    }

    public function createtotal(Request $req){
        $input = $req->input();        
        $arrInsert = array(
                "customer_id" => $input["sender_id"],
                "total" => $input["total"],
                "invoice_id" => date("ymd").Helpers::generateRandomString(),
                "created_at" => date("Y-m-d H:i:s")
            );
        $id = DB::table("inventory_customer_total_parcel")->insertGetId($arrInsert);
        $req->session()->put("new_parcel_id", $id);
        return redirect('/transaction/new')->with('message', "Successfull create");
    }

    public function delete(Request $req, $id){
        $customer = DB::table("inventory_transaction")->where("id", $id)->delete();        
        return redirect('/transaction')->with('message', "Successfull delete");
    }

    public function edit(Request $req, $id){
        $transactiondb = DB::table("inventory_transaction")->where("id", $id)->first();                
        
        if (isset($transactiondb->kecamatan_id)){
            $kecamatandb = DB::table("tb_rapid_tarif")->where("id", $transactiondb->kecamatan_id)->first();        
            $this->data["kecamatan"] = $kecamatandb;
        }
        if (isset($transactiondb->sender_id)){
            $customerdb = DB::table("inventory_customer")->where("id", $transactiondb->sender_id)->first();        
            $this->data["customer"] = $customerdb;        }
        
        
        $this->data["transaction"] = $transactiondb;
        return view('transaction.edit', $this->data);
    } 

     

    public function findtoprint(Request $req){
        $code = $req->input("order_no", ""); 
        $transactiondb = DB::table("inventory_transaction")->where("order_no", $code)->first();        
        if (isset($transactiondb->kecamatan_id)){
            $kecamatandb = DB::table("tb_rapid_tarif")->where("id", $transactiondb->kecamatan_id)->first();        
        }
        if (isset($transactiondb->sender_id)){
            $customerdb = DB::table("inventory_customer")->where("id", $transactiondb->sender_id)->first();        
        }


        $res = DNS2D::getBarcodePNG($code, "QRCODE", 5,5);
        $address = (isset($transactiondb->address) ? $transactiondb->address : "");
        if (isset($kecamatandb->kecamatan)){
            $address = $address.", ".$kecamatandb->kecamatan;
        }

        if (isset($kecamatandb->city)){
            $address = $address.", ".$kecamatandb->city;
        }

        $kecamatan = "";        
        if (isset($kecamatandb->kecamatan)){
            $kecamatan = $kecamatandb->kecamatan;
        }

        $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $transactiondb,"addrses" => $address, "kecamatan
            " => $kecamatan, "qrcode" => $res);
        return response()->json($res);
    }

    public function getcustomer(Request $req){
        $nama = $req->input("nama", "");
        $type = $req->input("type", "");
        $customers = DB::table("inventory_customer")
                    ->select("id","name","email")
                    ->where("name","like","%".$nama."%")->get();
        if ($type=="laporan_pengiriman"){
            $customers[]["id"]= "all";
            $customers[]["name"]= "All";
            $customers[]["email"]= "All@gmail.com";            
        }
        
        $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $customers);
        return response()->json($res);    
    }

    public function getkecamatan(Request $req){
        $nama = $req->input("nama", "");           
        $data = DB::select("SELECT *  FROM `tb_rapid_tarif` where city like '%".$nama."%' OR kecamatan like '%".$nama."%'");
        $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $data);
        return response()->json($res);    
    }

    public function newtotal(){                     
        return view('transaction.newtotal', $this->data);
    }

    public function newtrasaction(Request $req){
        $new_parcel_id = $req->session()->get("new_parcel_id", "");          
        $total_parcel_db = DB::table("inventory_customer_total_parcel")->where("id", $new_parcel_id)->first();
        if (!isset($total_parcel_db)){
            return redirect('/transaction');
        }
        $total_transaction = DB::table("inventory_transaction")->where("customer_total_parcel_id", $new_parcel_id)->count();  
        $customer = DB::table("inventory_customer_total_parcel")
                    ->join("inventory_customer", "inventory_customer.id", "=", "inventory_customer_total_parcel.customer_id")
                    ->where("inventory_customer_total_parcel.customer_id", $total_parcel_db->customer_id)
                    ->first();

        if ($total_transaction >= $total_parcel_db->total){
            return redirect('/transaction')->with('message', "Successfull create new transaction");
        }
        $this->data["new_parcel_id"] = $new_parcel_id;
        $this->data["total_transaction"] = $total_transaction;
        $this->data["total_parcel"] = $total_parcel_db;
        $this->data["customer"] = $customer;
        return view('transaction.new', $this->data);
    }

    public function taken(Request $req){
        $this->data["type"] = "Customer_taken";
        return view('transaction.taken', $this->data);
    }


    public function update(Request $req, $id){        
        $input = $req->input();
        $arrInsert = array(
                "sender_id" => $input["sender_id"],
                "kecamatan_id" => $input["city_id"],
                "phone" => $input["phone"],
                "weight" => $input["weight"],
                "address" => $input["address"],
                "reseller" => $input["reseller"],
                "receipt_name" => $input["receive"],
                "price" => 0
            );        
        $dbtarif = DB::table("tb_rapid_tarif")
            ->select("regular_price")
            ->where("id", $input["sender_id"])
            ->first();
        if (isset($dbtarif)){
            $arrInsert["price"] = $dbtarif->regular_price * (int)$arrInsert["weight"];
        }        
        DB::table("inventory_transaction")->where("id", $id)->update($arrInsert);        
        return redirect('/transaction')->with('message', "Successfull update");
    }

   

    private function _get_index_filter($input){
        $req = $this->data["req"];
        $transDB = DB::table("inventory_transaction")
            ->select("inventory_transaction.id", "inventory_transaction.reseller", "inventory_transaction.receipt_name", "inventory_transaction.created_at", "inventory_transaction.order_no",
                    "inventory_customer.name", "inventory_transaction.phone", "tb_rapid_tarif.kecamatan", "inventory_transaction.address", "inventory_transaction.weight","inventory_transaction.price","tb_rapid_tarif.city")
            ->leftJoin("inventory_customer", "inventory_customer.id", "=", "inventory_transaction.sender_id")
            ->leftJoin("tb_rapid_tarif", "tb_rapid_tarif.id", "=", "inventory_transaction.kecamatan_id");
        if ($req->session()->get("role")=="staff"){
            $transDB = $this->_get_role($transDB);
        }                             
        if(!empty($input["pelanggan"])){
            $transDB = $transDB->where("inventory_customer.name", "like" ,"%".$input["pelanggan"]."%");
        }
        if(!empty($input["penerima"])){
            $transDB = $transDB->where("inventory_transaction.receipt_name", "like" ,"%".$input["penerima"]."%");
        }
        if(!empty($input["awb"])){
            $transDB = $transDB->where("inventory_transaction.order_no", "like" ,"%".$input["awb"]."%");
        }
        if(!empty($input["kecamatan"])){
            $transDB = $transDB->where("tb_rapid_tarif.kecamatan", "like" ,"%".$input["kecamatan"]."%");
        }
        if(!empty($input["address"])){
            $transDB = $transDB->where("inventory_transaction.address", "like" ,"%".$input["address"]."%");
        }
        if(!empty($input["phone"])){
            $transDB = $transDB->where("inventory_transaction.phone", "like" ,"%".$input["phone"]."%");
        }
        if(!empty($input["weight"])){
            $transDB = $transDB->where("inventory_transaction.weight", "like" ,"%".$input["weight"]."%");
        }
        if(!empty($input["from"])){
            $transDB = $transDB->where(DB::raw("date(inventory_transaction.created_at)"), ">=" ,$input["from"]);
        }
        if(!empty($input["to"])){
            $transDB = $transDB->where(DB::raw("date(inventory_transaction.created_at)"), "<=" ,$input["to"]);
        }
        return $transDB;
    }

    private function _get_index_sort($req, $transDB, $input){                        
        if (isset($input["sort"])){                                
            if (empty($input["order_by"])){
                $order_by = "asc";       
            }else{
                $order_by = $input["order_by"];
            }
            $this->data["order_by"] = $order_by; 
            $this->data["sort"] = $input["sort"];

            if ($input["sort"]=="pelanggan"){
                if ($order_by == "asc"){
                    $this->data["arrow_pelanggan"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_pelanggan"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $transDB = $transDB->orderBy("inventory_customer.name", $order_by);                    
            } 
            else if ($input["sort"]=="reseller"){
                if ($order_by == "asc"){
                    $this->data["arrow_reseller"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_reseller"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.reseller", $order_by);                    
            }

            else if ($input["sort"]=="penerima"){
                if ($order_by == "asc"){
                    $this->data["arrow_penerima"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_penerima"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.receipt_name", $order_by);                    
            } else if ($input["sort"]=="awb"){
                if ($order_by == "asc"){
                    $this->data["arrow_awb"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_awb"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.order_no", $order_by);
            } else if ($input["sort"]=="city"){
                if ($order_by == "asc"){
                    $this->data["arrow_city"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_city"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("tb_rapid_tarif.city", $order_by);
            } else if ($input["sort"]=="kecamatan"){
                if ($order_by == "asc"){
                    $this->data["arrow_kecamatan"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_kecamatan"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("tb_rapid_tarif.kecamatan", $order_by);
            } else if ($input["sort"]=="address"){
                if ($order_by == "asc"){
                    $this->data["arrow_address"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_address"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.address", $order_by);
            }  else if ($input["sort"]=="phone"){
                if ($order_by == "asc"){
                    $this->data["arrow_phone"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_phone"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.phone", $order_by);
            }  
            else if ($input["sort"]=="weight"){
                if ($order_by == "asc"){
                    $this->data["arrow_weight"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_weight"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.weight", $order_by);
            }  
            else if ($input["sort"]=="harga"){
                if ($order_by == "asc"){
                    $this->data["arrow_harga"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_harga"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.price", $order_by);
            }  
            else if ($input["sort"]=="created_at"){
                if ($order_by == "asc"){
                    $this->data["arrow_created_at"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_created_at"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }                      
                $transDB = $transDB->orderBy("inventory_transaction.created_at", $order_by);
            }    

        }else{
            $transDB = $transDB->orderBy("inventory_transaction.created_at", "desc");
        }        
                           
        return $transDB;
    }

    private function _get_role($transDB){
        $kecamatan_id_arr = array();
        $agent_id = \Auth::user()->agent_id;
        if ($agent_id > 0 ){
            $agent_city = DB::table("agent")->select("city_id")->where("id", $agent_id)->first(); 
            if (isset($agent_city->city_id)){
                $city_kecamatan = DB::table("city_kecamatan")->where('city_id', $agent_city->city_id)->get();
                    
                foreach ($city_kecamatan as $key => $value) {
                    $kecamatan_id_arr[] = $value->kecamatan_id;
                }       
            }                
        }                
        $transDB = $transDB->whereIn("inventory_transaction.kecamatan_id", $kecamatan_id_arr)
                ->where("inventory_transaction.iscollect", 1);
        return $transDB;
    }

 
}