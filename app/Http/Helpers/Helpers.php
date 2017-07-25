<?php
	namespace App\Http\Helpers;		
	use \Session; 
	use App\Http\Helpers\WebCurl;
	use App\Http\Helpers\Api;
	use Illuminate\Support\Facades\DB;

	class Helpers{		
		public static function getIdInsert($req){
			$tbl_name = 'tb_merchant_pickup';
			$order_no = $req->input("order_id"," ");
			$is_awb_new = 0;
			if ($req->input("is_generate")=="1"){
				$is_awb_new = DB::table("inventory")->where("order_no", $order_no)->count();
				if ($is_awb_new==1){
					$order_no = self::get_rw_build();
				}				
				if ($req->input("rd_dest")=="address"){
					$tbl_name = 'tb_merchant_address';					
				}
			}
	        $ins = array(
	            "order_no" => $order_no,
	            "phone" => $req->input("phone"," "),
	            "recipient_name" => $req->input("nama",""),               
	            "email" => $req->input("email"," "),
	            "merchant_name" => $req->input("merchant"," "),	                        
	            "origin" => $req->input("origin_address",""),	            
	            "dest" => $req->input("dest_address",""),
	            "address_type" => strtoupper($req->input("rd_dest","")),
	            "isrounded" => $req->input("isrounded", "0"),
                "panjang" => $req->input("panjang", "0"),
                "lebar" => $req->input("lebar", "0"),
                "tinggi" => $req->input("tinggi", "0"),
                "weight" => $req->input("weight", "0"),
                "oweight" => $req->input("oweight", "0"),
                "rweight" => $req->input("rweight", "0"),
	            "tbl_name" => $tbl_name,	           
	            "upload_date" =>date("Y-m-d H:i:s")
	            );

	       	$ins_member = $ins;
	        $isexist = DB::table("inventory")
            	->where("order_no", $ins["order_no"])
            	->whereNull("deleted_at")
            	->first();            
            if (isset($isexist)){            	
            	return null;
            }else{            	
            	$ins["is_generate"] = $req->input("is_generate");
            	$ins["resi_no"] = $req->input("resi_no", "");
            	$id  = DB::table("inventory")->insertGetId($ins);        
            	if ($tbl_name == "tb_merchant_pickup"){
	            	$count = DB::table("tb_merchant_pickup")->where("order_number", $ins["order_no"])->count();
	            	if ($count==0){
	            		 $insPickup = array(
				        	"order_number" =>$ins["order_no"],
				        	"merchant_name" => $req->input("merchant"," "),
				        	"customer_phone" => $req->input("phone"," "),
				        	"customer_email" => $req->input("email"," "),
				        	"weight" => $req->input("weight","0"),
				        	"pickup_location" => $req->input("origin_address",""),
				        	"popbox_location" => $req->input("dest_address",""),				        	
				        	"last_update" => date("Y-m-d H:i:s")
		        		);
	            		DB::table("tb_merchant_pickup")->insert($insPickup);
	            	}
            	}else{
            		$count = DB::table("tb_merchant_address")->where("order_no", $ins["order_no"])->count();
	            	if ($count==0){	        
	            		DB::table("tb_merchant_address")->insert($ins_member);
	            	}
            	}
            	return array("id" => $id, "is_awb_new" => $is_awb_new, "order_no" => $ins["order_no"]);
            } 
	        
	    }

	    public static function insertInvetoryHistory($id, $req, $user, $arr_is_status){
	    	 $status = $req->input("type");	    	
	    	// $delivery_type = $req->input("del_type", "");
	    	// if (strtolower($delivery_type)=="pilih"){	    			    		
	    	if ($arr_is_status["is_return"]){
	    		$delivery_type = "return";
	    	}else if ($arr_is_status["is_delivery"]){
	    		$delivery_type = "delivery";
	    	}else{
	    		$delivery_type = "";
	    	}
	    	
	    	$ins_history = array(                
                "id_inv" => $id,
                "order_no" => $req->input("orderNo",""),
                "inventory_courier_id" => $req->session()->get("courier", ""),                               
                "status" => $status,
                "remark" => $req->input("remark" , ""),
                "logistic_name" => $user->first_name." ".$user->last_name,
                "delivery_type" => $delivery_type,
                "last_update" => date("Y-m-d H:i:s")
            );

            $isexist = DB::table("inventory_history")
            	->where("order_no", $ins_history["order_no"])
            	->where("status", $status)
            	->where("last_update",">", date("Y-m-d H:i").":00")
            	->whereNull("deleted_at")
            	->first();            
            if (isset($isexist)){
            	die();
            }else{
            	DB::table("inventory_history")->insert($ins_history);            	
            }
	    }

	    public static function insertInvetory($data, $req){
 			$data["isrounded"] 	= $req->input("isrounded", "0");
            $data["panjang"] 	= $req->input("panjang", "0");
            $data["lebar"] 		= $req->input("lebar", "0");
            $data["tinggi"]	 	= $req->input("tinggi", "0");            
	    	$id = DB::table("inventory")->insertGetId($data);
	    	return $id;
	    }

	    public static function get_inventory_table($inventory, $type){
	    	if ($type == "member_pikcup"){
	    		return self::get_inventory_member_pickup($inventory);	    		
	    	}else if ($type == "merchant_pickup"){
	    		return 	self::get_inventory_merchant_pickup($inventory);	    		
	    	}else if ($type == "merchant_return"){	    
	    		return self::get_inventory_merchant_return($inventory);	    		
	    	}else if ($type == "orders"){	    		
	    		return self::get_inventory_order($inventory);	    		
	    	}else if ($type == "merchant_service"){	    		
	    		return self::get_inventory_service($inventory);	    		
	    	}else if ($type == "inventory_transaction"){
	    		return self::get_inventory_transaction($inventory);	    		

	    	}
	    }

	    public static function get_inventory_transaction($inventory){
	    	$customernameDb = DB::table("inventory_customer")->where("id", $inventory->sender_id)->first();
	    	return array(
	    		"order_no" 			=> $inventory->order_no,
	    		"merchant_name" 	=> "Popbox Asia",
	    		"phone" 			=> $inventory->phone, 
	    		"email" 			=> "",
	    		"origin" 			=> "Popbox Asia WH",
	    		"address_type" 		=> "ALAMAT",
	    		"dest" 				=> $inventory->address,		
	    		'recipient_name' 	=> $customernameDb->name,
	    		"tbl_name" 			=> 'inventory_transaction',
	    		"upload_date" 		=> date('Y-m-d H:i:s')
	    		);
	    }

	    public static function get_inventory_member_pickup($inventory){
	    	$dest = $inventory->recipient_address.", ".$inventory->recipient_address_detail;
	    	if (strtoupper(substr($inventory->invoice_id, 0, 3)) == 'PLL') {
	    		$dest = $inventory->recipient_locker_name;
	    	}	    	
	    	$data = array(
	    		"order_no" 			=> $inventory->invoice_id,
	    		"merchant_name" 	=> "Popbox Asia",
	    		"phone" 			=> $inventory->recipient_phone, 
	    		"email" 			=> $inventory->recipient_email, 
	    		"origin" 			=> $inventory->pickup_locker_name,
	    		"address_type" 		=> "ALAMAT",
	    		"dest" 				=> $dest,	    		
	    		'recipient_name' 	=> $inventory->recipient_name,
	    		"tbl_name" 			=> 'tb_member_pickup',
	    		"upload_date" 		=> date('Y-m-d H:i:s')
	    		);
	    	return $data;
	    }

	    public static function get_inventory_merchant_pickup($inventory){
	    	$data = array(
	    		"order_no" 			=> $inventory->order_number,
	    		"merchant_name" 	=> $inventory->merchant_name,
	    		"phone" 			=> $inventory->customer_phone, 
	    		"email" 			=> $inventory->customer_email, 
	    		"origin" 			=> $inventory->pickup_location, 
	    		"address_type" 		=> "LOKER",
	    		"dest" 				=> $inventory->popbox_location,	    		
	    		"tbl_name" 			=> 'tb_merchant_pickup',
	    		"recipient_name" 	=> "-",
	    		"oweight"			=> $inventory->weight,
	    		"rweight"			=> $inventory->weight,
	    		"upload_date" 		=> date('Y-m-d H:i:s')
	    		);
	    	return $data;
	    }

	    public static function get_inventory_merchant_return($inventory){
	    	$merchant_return = DB::table("tb_merchant_return")
	    		->where("merchant_name", $inventory->merchant_name)
	    		->first();
	    	$dest = $inventory->merchant_name;
	    	if (isset($merchant_return)){
	    		if (isset($merchant_return->seller_address)){
	    			$dest = $merchant_return->seller_address;	
	    		}	    		
	    	}
	    	$data = array(
	    		"order_no" 			=> $inventory->tracking_no,
	    		"merchant_name" 	=> $inventory->merchant_name,
	    		"phone" 			=> "", 
	    		"email" 			=> "", 
	    		"origin" 			=> $inventory->locker_name, 
	    		"address_type" 		=> "ALAMAT",
	    		"dest" 				=> $dest,	    		
	    		"tbl_name" 			=> 'locker_activities_return',
	    		"recipient_name" 	=> $inventory->merchant_name,
	    		"upload_date" 		=> date('Y-m-d H:i:s')
	    		);
	    	return $data;
	    }

	     public static function get_inventory_order($inventory){	
	    	$data = array(
	    		"order_no" 			=> $inventory->invoice_id,
	    		"merchant_name" 	=> "PopBox Asia",
	    		"phone" 			=> $inventory->phone,
	    		"email" 			=> $inventory->email,
	    		"origin" 			=> "PopBox Asia",
	    		"dest" 				=> $inventory->address,
	    		"address_type" 		=> "LOKER",
	    		"tbl_name" 			=> 'orders',
	    		"recipient_name" 	=> $inventory->customer_name,
	    		"upload_date" 		=> date('Y-m-d H:i:s')
	    		);
	    	return $data;
	    }	    

	    public static function get_inventory_service($inventory){
	    	$merchant = DB::table("companies")->where("prefix", $inventory->prefix)->first();
	    	$data = array(
	    		"order_no" 			=> $inventory->invoice_id,
	    		"merchant_name" 	=> $merchant->name,	    		
	    		"email" 			=> $inventory->cust_email,
	    		"origin" 			=> "",	    		
	    		"tbl_name" 			=> 'tb_merchant_service',	    		
	    		"upload_date" 		=> date('Y-m-d H:i:s')
	    	);
	    	if ( $inventory->status == "COURIER_TAKEN" && $inventory->merchant_status == "PROCESS COMPLETED" ){
	    		$data["dest"] = $inventory->locker;
	    		$data["phone"] = $inventory->cust_phone;
	    		$data["recipient_name"] = $inventory->cust_name;
	    		$data["address_type"] = "LOKER";
	    	}else{
	    		$data["dest"] = $merchant->name;
	    		$data["recipient_name"] = $merchant->name;
	    		$data["phone"] = "-";
	    		$data["address_type"] = "ADDRESS";
	    	}
	    	return $data;
	    }

	     public static function get_rw_build() {
	    	$pref = date("ymd");		
	    	$sql = "SELECT count(*) as total from inventory  where SUBSTRING(order_no,1,6)='".$pref."' AND is_generate=1 AND deleted_at is null";	
			$countdb = DB::select($sql);	
			$prefix = "000".($countdb[0]->total+1);
			$prefix = substr($prefix,-4);		
		return date("ymd").$prefix;
		}

		public static function get_rw_build_transaction() {
	    	$pref = date("ymd");		
	    	$sql = "SELECT count(*) as total from inventory_transaction  where SUBSTRING(order_no,1,6)='".$pref."'";	
			$countdb = DB::select($sql);	
			$prefix = "000".($countdb[0]->total+1);
			$prefix = substr($prefix,-4);		
			return date("ymd").$prefix;
		}

		public static function generateRandomString($length = 4) {
			  $characters = '0123456789';
			  $randomString = '';
			  for ($i = 0; $i < $length; $i++) {
			    $randomString .= $characters[rand(0, strlen($characters) - 1)];
			  }			  
			  return $randomString;
		}


		public static function find_courier($name){
			$courier = DB::table("inventory_courier")->select("id")->where("name", $name)->first();
			if (isset($courier)){				
				return $courier->id;
			}else{
				$ins = array("company_id" => 1, "name"=> $name, "created_at" => date("Y-m-d h:i:s"));
				$id = DB::table("inventory_courier")->insertGetId($ins);
				return $id;
			}
		}

		
	}
?>