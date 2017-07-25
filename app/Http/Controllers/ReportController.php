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

class ReportController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "transaction"; 
        $this->data["req"] = $req;        
    }

	
    public function biaya_excel(){
        $req = $this->data["req"];
        $input = $req->input();
        if (isset($input["pelanggan"])){            
            $this->get_data_report($input);
            $xls = new PHPExcel();

            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath(base_path().'/public/img/logo-bks.png');
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($xls->getActiveSheet());
            $objDrawing->setWidth("150");
            $xls->getActiveSheet()->getRowDimension(0)->setRowHeight(20);


            $xls->getActiveSheet()->setCellValue('E1', 'Branch');
            $xls->getActiveSheet()->setCellValue('E2', 'Pengirim');
            $xls->getActiveSheet()->setCellValue('E3', 'Tanggal');
            $xls->getActiveSheet()->setCellValue('E4', 'INVOICE');
            $xls->getActiveSheet()->mergeCells('F1:G1');
            $xls->getActiveSheet()->setCellValue('F1', ': JAKARTA');
            $xls->getActiveSheet()->mergeCells('F2:G2');
            $xls->getActiveSheet()->setCellValue('F2', ": ".$this->data["customer"]->name);
            $xls->getActiveSheet()->mergeCells('F3:G3');
            $xls->getActiveSheet()->setCellValue('F3', ": ".$input["from"]);
            $xls->getActiveSheet()->mergeCells('F4:G4');
            $xls->getActiveSheet()->setCellValue('F4', ": ".$this->data["total_parcel"]->invoice_id);

            $xls->getActiveSheet()->setCellValue('A5', 'No');
            $xls->getActiveSheet()->setCellValue('B5', 'No Resi');
            $xls->getActiveSheet()->setCellValue('C5', 'Tujuan');
            $xls->getActiveSheet()->setCellValue('D5', 'Penerima');
            $xls->getActiveSheet()->setCellValue('E5', 'Harga sebelum discount');
            $xls->getActiveSheet()->setCellValue('F5', 'Discount');
            $xls->getActiveSheet()->setCellValue('G5', 'Harga Setelah Discount');        

            $i=0;
            $tot_discount = 0;
            $tot_biaya = 0;
            $idx = 6;
            foreach($this->data["transaction"] as $trans){
                $discount =  $trans->price - ($trans->price* ($this->data["customer"]->discount/100));
                $tot_discount = $tot_discount+$discount;
                $tot_biaya = $tot_biaya + $trans->price;
                $xls->getActiveSheet()->setCellValue('A'.$idx, ++$i);
                $xls->getActiveSheet()->setCellValue('B'.$idx, $trans->order_no);
                $xls->getActiveSheet()->setCellValue('C'.$idx, $trans->kecamatan);
                $xls->getActiveSheet()->setCellValue('D'.$idx, $tarns->receipt_name);
                $xls->getActiveSheet()->setCellValue('E'.$idx, "Rp ".number_format($trans->price));
                $xls->getActiveSheet()->setCellValue('F'.$idx, $this->data["customer"]->discount." %");
                $xls->getActiveSheet()->setCellValue('G'.$idx, "Rp ".number_format($discount));
                $idx++;
            }
            
            $xls->getActiveSheet()->setCellValue('A'.$idx ,'Total Biaya ');                        
            $xls->getActiveSheet()->mergeCells('B'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('B'.$idx ,": Rp ".number_format($tot_biaya));                        
            $idx++;
            $xls->getActiveSheet()->setCellValue('A'.$idx, "Jumlah paket");
            $xls->getActiveSheet()->mergeCells('B'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('B'.$idx, ": ".count($this->data["transaction"]));
            $idx++;
            $xls->getActiveSheet()->setCellValue('A'.$idx, "Total Tagihan");
            $xls->getActiveSheet()->mergeCells('B'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('B'.$idx ,": Rp ".number_format($tot_discount));                        
            
            $idx++;$idx++;$idx++;
            $xls->getActiveSheet()->mergeCells('A'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('A'.$idx, "Mohon untuk dapat melakukan pembayaran melalui rekening berikut :");

            $idx++;
            $xls->getActiveSheet()->mergeCells('A'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('A'.$idx, "1. Bank BCA No. Rek. 5260 3588 22 Atas Nama PT PopBox Asia Service");
            
            $idx++;
            $xls->getActiveSheet()->mergeCells('A'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('A'.$idx, "2. Bank Mandiri No. Rek. 1650 0091 2222 8 Atas Nama PT PopBox Asia Service");

            $idx++;
            $xls->getActiveSheet()->mergeCells('A'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('A'.$idx, "3. Bank BNI No. Rek. 8088 0019 46 Atas Nama PT PopBox Asia Services");

            $idx++;$idx++;$idx++;
            $xls->getActiveSheet()->mergeCells('A'.$idx.':H'.$idx);
            $xls->getActiveSheet()->setCellValue('A'.$idx, "Silahkan menghubungi Customer Care di nomor 021-29022537 atau melalui info@popbox.asia jika mengalami kendala atau ketidaksesuaian");

            $idx++;
            $xls->getActiveSheet()->mergeCells('A'.$idx.':G'.$idx);
            $xls->getActiveSheet()->setCellValue('A'.$idx, "Untuk info lebih lanjut dapat di akses melalui https://www.popbox.asia");

            $writer = new \PHPExcel_Writer_Excel5($xls);   
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_pelanggan-'.$this->data["customer"]->name.'-'.date("Y-m-d").'.xls"');
            header('Cache-Control: max-age=0');         
            $writer->save('php://output');      
        }    
    }
    

    public function findtoprint(){
        $req = $this->data["req"];
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

        $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $transactiondb,"address" => $address, "kecamatan" => $kecamatan, "qrcode" => $res);
        return response()->json($res);
    }

    public function getcustomer(){
        $req = $this->data["req"];
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

    public function getkecamatan(){
        $req = $this->data["req"];
        $nama = $req->input("nama", "");           
        $data = DB::select("SELECT *  FROM `tb_rapid_tarif` where city like '%".$nama."%' OR kecamatan like '%".$nama."%'");
        $res = array("response"=>array("code"=>200 , "messsage" => "ok"), "data" => $data);
        return response()->json($res);    
    }

    public function biaya(Request $req){        
        $this->data["parameter"] = $_SERVER['QUERY_STRING'];        
        $this->data["type"]= "Laporan_Pelanggan";
        $input = $req->input();
        if (isset($input["pelanggan"])){            
            $this->get_data_report($input);
            $this->data["input"] = $input;
        }
        return view('report.biaya', $this->data);
    }

    public function pengiriman(){
        $req = $this->data["req"];
        $this->data["parameter"] = $_SERVER['QUERY_STRING'];        
        $this->data["type"]= "Laporan_Pengiriman";
        $input = $req->input();                         
        if (isset($input["from"]) && isset($input["to"])){
            $transDB = $this->_get_laporan_pengiriman($input);            
            $transDB = $transDB->get();                        
            $this->data["transaction"] = $transDB;            
        }

        $this->data["input"] = $input;
        return view('report.pengiriman', $this->data);
    }

    public function pengiriman_excel(){
        $req = $this->data["req"];
        $input = $req->input();                         
        if (isset($input["from"]) && isset($input["to"])){
            $transDB = $this->_get_laporan_pengiriman($input);            
            $transDB = $transDB->get();                        
            $xls = new PHPExcel();
            $xls->getActiveSheet()->setCellValue('A1', 'No');
            $xls->getActiveSheet()->setCellValue('B1', 'Pelanggan');
            $xls->getActiveSheet()->setCellValue('C1', 'Reseller');
            $xls->getActiveSheet()->setCellValue('D1', 'Penerima');
            $xls->getActiveSheet()->setCellValue('E1', 'AWB');
            $xls->getActiveSheet()->setCellValue('F1', 'City');
            $xls->getActiveSheet()->setCellValue('G1', 'Kecamatan');
            $xls->getActiveSheet()->setCellValue('H1', 'Address');
            $xls->getActiveSheet()->setCellValue('I1', 'Phone');
            $xls->getActiveSheet()->setCellValue('J1', 'weight');
            $xls->getActiveSheet()->setCellValue('K1', 'Harga');
            $xls->getActiveSheet()->setCellValue('L1', 'Status');
            $xls->getActiveSheet()->setCellValue('M1', 'Keterangan');
            $xls->getActiveSheet()->setCellValue('N1', 'Created at');

            $i=0;
            $tot_discount = 0;
            $idx = 2;

            foreach($transDB as $value){
                $type = "on proses";
                $desc_type = "";
                if ($value->type=="0"){
                    $type = "Bermasalah";
                    $desc_type = $value->description_problem;
                }else if ($value->type=="1"){
                    $type = "Di terima";
                    $desc_type = $value->penerima." (".$value->status.")";
                }
                $xls->getActiveSheet()->setCellValue('A'.$idx, ++$i);
                $xls->getActiveSheet()->setCellValue('B'.$idx, $value->name);
                $xls->getActiveSheet()->setCellValue('C'.$idx, $value->reseller);
                $xls->getActiveSheet()->setCellValue('D'.$idx, $value->receipt_name);
                $xls->getActiveSheet()->setCellValue('E'.$idx, $value->order_no);
                $xls->getActiveSheet()->setCellValue('F'.$idx, $value->city);
                $xls->getActiveSheet()->setCellValue('G'.$idx, $value->kecamatan);
                $xls->getActiveSheet()->setCellValue('H'.$idx, $value->address);
                $xls->getActiveSheet()->setCellValue('I'.$idx, $value->phone);
                $xls->getActiveSheet()->setCellValue('J'.$idx, $value->weight);
                $xls->getActiveSheet()->setCellValue('K'.$idx, "Rp ".number_format($value->price));
                $xls->getActiveSheet()->setCellValue('L'.$idx, $type);
                $xls->getActiveSheet()->setCellValue('M'.$idx, $desc_type);
                $xls->getActiveSheet()->setCellValue('N'.$idx, $value->created_at);
                $idx++;
            }

            // $xls->getActiveSheet()->mergeCells('A'.$idx.':F'.$idx);
            // $xls->getActiveSheet()->setCellValue('A'.$idx ,'Total discount :');                        
            // $xls->getActiveSheet()->setCellValue('G'.$idx, "Rp ".number_format($tot_discount));

            $writer = new \PHPExcel_Writer_Excel5($xls);        
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_pengiriman-'.$input["customer"]."-".date("Y_m_d").'.xls"');
            header('Cache-Control: max-age=0');         
            $writer->save('php://output');      
        }

    }

    public function biaya_pdf(){
        $req = $this->data["req"];
        $input = $req->input();
        if (isset($input["pelanggan"])){
            $this->get_data_report($input);
            $this->data["input"] = $input;
            $this->data["parameter"] = $_SERVER['QUERY_STRING'];
            $pdf = \PDF::loadView('report.pdf', $this->data);
            return $pdf->download('laporan_pelanggan-'.$input["pelanggan"].'-'.date("Y-m-d").'.pdf');
        }
    }

     public function pdfview(){   
        $req = $this->data["req"];     
        $input = $req->input();
        if (isset($input["pelanggan"])){
            $this->get_data_report($input);
            $this->data["input"] = $input;
            $this->data["parameter"] = $_SERVER['QUERY_STRING'];        
            return view('report.pdf', $this->data);
            // $pdf = \PDF::loadView('transaction.pdf', $this->data);
            // return $pdf->download($this->data["customer"]->name.'-'.date("Ymd").'-transaction.pdf');
        } 
    }

    public function newtotal(){             
        return view('transaction.newtotal', $this->data);
    }

    public function newtrasaction(){
        $req = $this->data["req"];
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

    public function taken(){
        $this->data["type"] = "Customer_taken";
        return view('transaction.taken', $this->data);
    }


    public function update($id){ 
        $req = $this->data["req"];       
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

    private function get_data_report($input){
        $this->data["customer"] = DB::table("inventory_customer")
                ->where("id", $input["sender_id"])
                ->first();
        $transactiondb = DB::table("inventory_transaction")
                ->select("inventory_transaction.id", "inventory_transaction.order_no", "inventory_transaction.weight",
                 "inventory_transaction.price","inventory_transaction.receipt_name", "inventory_transaction.customer_total_parcel_id",
                 "tb_rapid_tarif.kecamatan")
                ->leftJoin("tb_rapid_tarif","tb_rapid_tarif.id","=","inventory_transaction.kecamatan_id")
                ->where("sender_id", $input["sender_id"])
                ->where(DB::raw("date(created_at)"), ">=", $input["from"])
                ->where(DB::raw("date(created_at)"), "<=", $input["to"])
                ->get();          
        if (isset($transactiondb[0])){
            $parcel_id =  $transactiondb[0]->customer_total_parcel_id;
            $total_parcel_db = DB::table("inventory_customer_total_parcel")
                                ->where("id", $parcel_id)
                                ->first();    
            $this->data["total_parcel"] = $total_parcel_db;
        }

        $this->data["transaction"] = $transactiondb;
        
    }

    private function _get_laporan_pengiriman($input){
        $transDB = DB::table("inventory_transaction")
            ->select("inventory_transaction.id", "inventory_transaction.reseller", "inventory_transaction.receipt_name", "inventory_transaction.created_at", "inventory_transaction.order_no",
                    "inventory_customer.name", "inventory_transaction.phone", "tb_rapid_tarif.kecamatan", "inventory_transaction.address", 
                    "inventory_transaction.weight","inventory_transaction.price","tb_rapid_tarif.city",
                    "inventory_taken.type", "inventory_taken.description_problem", "inventory_taken.penerima", "inventory_taken.status")
            ->leftJoin("inventory_customer", "inventory_customer.id", "=", "inventory_transaction.sender_id")
            ->leftJoin("tb_rapid_tarif", "tb_rapid_tarif.id", "=", "inventory_transaction.kecamatan_id")
            ->leftJoin("inventory_taken", "inventory_taken.order_no", "=", "inventory_transaction.order_no")                                                
            ->where(DB::raw("date(inventory_transaction.created_at)"), ">=", $input["from"])            
            ->where(DB::raw("date(inventory_transaction.created_at)"), "<=", $input["to"])
            ->groupBy("inventory_transaction.order_no");           
        if (strtolower($input["customer"])!="all"){
            $transDB = $transDB->where("inventory_customer.name", "like", "%".$input["customer"]."%");
        }
        return $transDB;
    }

    private function _get_index_filter($input){
        $transDB = DB::table("inventory_transaction")
            ->select("inventory_transaction.id", "inventory_transaction.reseller", "inventory_transaction.receipt_name", "inventory_transaction.created_at", "inventory_transaction.order_no",
                    "inventory_customer.name", "inventory_transaction.phone", "tb_rapid_tarif.kecamatan", "inventory_transaction.address", "inventory_transaction.weight","inventory_transaction.price","tb_rapid_tarif.city")
            ->leftJoin("inventory_customer", "inventory_customer.id", "=", "inventory_transaction.sender_id")
            ->leftJoin("tb_rapid_tarif", "tb_rapid_tarif.id", "=", "inventory_transaction.kecamatan_id");                              
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

 
}