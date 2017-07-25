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

class PriceController extends Controller {
    
    var $data;
    public function __construct(Request $req){
    	$this->data["type"]= "transaction";        
        $this->data["req"] = $req;   
    }

	 public function getList(){
        $req = $this->data["req"];
        $input = $req->input();        
        $priceDB = $this->_get_index_filter($input);              
        $priceDB = $priceDB->paginate(20);        
        $this->data["price"] = $priceDB;
        $this->data["filter"] = $input;
        $helpers = new Helpers();
        $this->data["helpers"] = new Helpers();                     
        return view('price.index', $this->data);
    }

    public function getEdit($id){
        $req = $this->data["req"];
        $price = DB::table("tb_rapid_tarif")->where("id", $id)->first();
        $this->data["price"] = $price;
        return view('price.edit', $this->data);
    }

    public function getDelete($id){        
        $req = $this->data["req"];
        DB::table("tb_rapid_tarif")->where("id", $id)->delete();        
        return redirect('/price/list')->with('message', "Successfull delete");    
    }

    public function postUpdate($id){
        $req = $this->data["req"];
        $arrInsert = $req->input();        
        unset($arrInsert["_token"]);        
        DB::table("tb_rapid_tarif")->where("id", $id)->update($arrInsert);        
        return redirect('/price/list')->with('message', "Successfull update");
    }

    public function postUpload(){
        $msg = "";  
        $arrMsg = array();
         if(Input::file('fileupload')!=null){
            $file =Input::file('fileupload');
            $objPHPExcel = PHPExcel_IOFactory::load($file);                        
            $arr = array();            
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $worksheetTitle     = $worksheet->getTitle();
                $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $nrColumns = ord($highestColumn) - 64;
                // echo "<br>The worksheet ".$worksheetTitle." has ";
                // echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
                // echo ' and ' . $highestRow . ' row.';
                // echo '<br>Data: <table border="1"><tr>';
                for ($row = 2; $row <= $highestRow; ++ $row){                    
                    for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();
                        $new_row = $row-2;                        
                        $i = 0;
                        if ($col==$i++){
                            $arr[$new_row]["area_code"] = $val;
                            if (!isset($val))
                                $arrMsg[] = "Area code";
                        }
                        else if ($col==$i++)
                            $arr[$new_row]["city"] = $val;
                            if (!isset($val))
                                $arrMsg[] = "City";
                        else if ($col==$i++){
                            $arr[$new_row]["kecamatan"] = $val;
                            if (!isset($val))
                                $arrMsg[] = "Kecamatan";
                        }
                         else if ($col==$i++){
                            $arr[$new_row]["city_code"] = $val;
                            if (!isset($val))
                                $arrMsg[] = "City Code";
                        }
                        else if ($col==$i++){
                            $arr[$new_row]["regular_price"] = $val;
                            if (!isset($val))
                                $arrMsg[] = "Regular Price";
                        }
                        else if ($col==$i++){
                            $arr[$new_row]["est_delivery"] = $val;
                            if (!isset($val))
                                $arrMsg[] = "Estimation Delivery";
                        }
                        else if ($col==$i++)
                            $arr[$new_row]["oneday_price"] = $val;
                        if (!isset($val))
                                $arrMsg[] = "One Day";
                        $arr[$new_row]["last_update"] = date("Y-m-d H:i:s");
                        
                    }
                }                
            }            
            if (count($arrMsg) > 0) {
                $arrMsg = array_unique($arrMsg);
                $msg = "<strong>".implode(",",$arrMsg)."</strong> cannot be black, please complete your upload data";
                return redirect('/price/list')->with('message', $msg);
                die();
            }
            if (count($arr)>0){
                foreach ($arr as $key => $value) {
                    $dbtarif = DB::table("tb_rapid_tarif")->where("area_code", $value["area_code"])->first();
                    if (isset($dbtarif)){
                        DB::table("tb_rapid_tarif")->where("area_code", $value["area_code"])->update($value);
                    }else{
                        DB::table("tb_rapid_tarif")->insert($value);
                    }

                }                
                $msg = "Data berhasil di upload";
            }        
        }
        return redirect('/price/list')->with('message', $msg);
    }

    private function _get_index_filter($filter){                
        $req = $this->data["req"];
        $priceDB = DB::table("tb_rapid_tarif");    
        if (isset($filter["city"])){
            $priceDB = $priceDB->where("city", "like", "%".$filter["city"]."%");
        } 
        if (isset($filter["kecamatan"])){
            $priceDB = $priceDB->where("kecamatan", "like", "%".$filter["kecamatan"]."%");
        }    
        $priceDB = $priceDB->orderBy("id", "desc");
        return $priceDB;
    }

    private function _get_index_sort($req, $transDB, $input){                        
        
    }

     
}