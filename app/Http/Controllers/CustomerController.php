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

class CustomerController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "master_customer";  
        $this->data["req"] = $req;    	
    }

	public function index(){                
        $req = $this->data["req"];
        $input= $req->input();         
        $custDB = $this->_get_index_filter($input);        
        $custDB = $this->_get_index_sort($req, $custDB, $input);           
        $custDB = $custDB->get();           
        // echo $req->session()->get('role');
        $this->data["input"] = $input;
        $this->data["cust"] = $custDB;
        return view('customer.index', $this->data);
    }

    public function create(){
        $req = $this->data["req"];
        $validator = Validator::make($req->all(), [            
            'name' => 'required|unique:inventory_customer|max:100'          
        ]);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }
        $arrInsert = $req->input();
        $arrInsert["created_at"] = date("Y-m-d h:i:s");
        unset($arrInsert["_token"]);        
        DB::table("inventory_customer")->insert($arrInsert);        
        return redirect('/customer')->with('message', "Successfull create");
    }

    public function edit($id){
        $customer = DB::table("inventory_customer")->where("id", $id)->first();        
        $this->data["customer"] = $customer;
        return view('customer.edit', $this->data);        
    }

    public function delete($id){
        $req = $this->data["req"];
        DB::table("inventory_customer")->where("id", $id)->delete();        
        DB::table("inventory_transaction")->where("sender_id", $id)->delete();        
        DB::table("inventory_customer_total_parcel")->where("customer_id", $id)->delete();        
        return redirect('/customer')->with('message', "Successfull delete");
    }

    public function newcustomer(){
        return view('customer.new', $this->data);
    }

    public function update($id){
        $req = $this->data["req"];
        $arrInsert = $req->input();        
        unset($arrInsert["_token"]);        
        $customer = DB::table("inventory_customer")->where("id", $id)->update($arrInsert);
        $this->data["customer"] = $customer;
        return redirect('/customer')->with('message', "Successfull update");
    }

    private function _get_index_filter($filter){
        $dbcust = DB::table("inventory_customer");
        if (isset($filter["name"])){
            $dbcust = $dbcust->where("name", "like", "%".$filter["name"]."%");
        }
        if (isset($filter["owner"])){
            $dbcust = $dbcust->where("owner", "like", "%".$filter["owner"]."%");
        }
        if (isset($filter["phone"])){
            $dbcust = $dbcust->where("phone", "like", "%".$filter["phone"]."%");
        }
        if (isset($filter["email"])){
            $dbcust = $dbcust->where("email", "like", "%".$filter["email"]."%");
        }
        if (isset($filter["address"])){
            $dbcust = $dbcust->where("address", "like", "%".$filter["address"]."%");
        }
        return $dbcust;
    }

    private function _get_index_sort($req, $custDB, $input){                        
        if (isset($input["sort"])){
            if (empty($input["order_by"])){
                $order_by = "asc";       
            }else{
                $order_by = $input["order_by"];
            }
            $this->data["order_by"] = $order_by; 
            $this->data["sort"] = $input["sort"];

            if ($input["sort"]=="nama"){
                if ($order_by == "asc"){
                    $this->data["arrow_nama"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_nama"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("name", $order_by);                                
            }
            else if ($input["sort"]=="owner"){
                if ($order_by == "asc"){
                    $this->data["arrow_owner"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_owner"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("owner", $order_by);                                
            }
            else if ($input["sort"]=="email"){
                if ($order_by == "asc"){
                    $this->data["arrow_email"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_email"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("email", $order_by);                                
            }
            else if ($input["sort"]=="discount"){
                if ($order_by == "asc"){
                    $this->data["arrow_discount"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_discount"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("discount", $order_by);                                
            }
            else if ($input["sort"]=="phone"){
                if ($order_by == "asc"){
                    $this->data["arrow_phone"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_phone"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("phone", $order_by);                                
            }
            else if ($input["sort"]=="address"){
                if ($order_by == "asc"){
                    $this->data["arrow_address"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_address"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("address", $order_by);                                
            }
            else if ($input["sort"]=="created"){
                if ($order_by == "asc"){
                    $this->data["arrow_created"] = '<span class="glyphicon glyphicon-menu-down"></span>';
                }elseif ($order_by == "desc"){
                    $this->data["arrow_created"] = '<span class="glyphicon glyphicon-menu-up"></span>';
                }      
                $custDB = $custDB->orderBy("created_at", $order_by);                                
            }
        }else{
            $custDB = $custDB->orderBy("id", "desc");
        }        
                           
        return $custDB;
    }

}
    