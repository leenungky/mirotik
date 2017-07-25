<?php namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\User;
use Socialize;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Redirect; 
use Illuminate\Support\Facades\Input;
use App\Lib\SiteHelpers;
use \DB;

class RoleController extends Controller {

	
	protected $layout = "layouts.main";

	public function __construct(Request $req) {
		$this->data["type"]= "Role";      
		$this->data["req"] = $req;
	} 

	// batas atas

	public function getAdd(){
		$role = DB::table("tb_role")->get();		
		$this->data["role"] = $role;
		return view('user.new', $this->data);  
	}
	public function getList(){		
		$this->data["roles"] = DB::table("tb_role")->get();				
		return view('role.list', $this->data);  
	}

	public function getEdit($id){		
		$role = DB::table("tb_role")->where("id" , $id)->first();							
		$role_user_priviledge = DB::select("select priviledge_id from role_priviledge where role_priviledge.role_id=".$id);
		$priviledge_id_arr = array();
		foreach ($role_user_priviledge as $key => $value) {
			$priviledge_id_arr[] = $value->priviledge_id;
		}				
		$priviledges = DB::table("priviledge")->whereNotIn("id", $priviledge_id_arr)->orderBy("name", "desc")->get();			
		$result_priviledges = DB::table("priviledge")->whereIn("id", $priviledge_id_arr)->orderBy("name", "desc")->get();			
		$this->data["role"] = $role;
		$this->data["priviledges"] = $priviledges;
		$this->data["result_priviledges"] = $result_priviledges;
		
		return view('role.edit', $this->data);  
	}

	public function getRolepriviledge(){		
		$role_priviledges = DB::table("role_priviledge")
			->join("priviledge","priviledge.id", "=", "role_priviledge.priviledge_id")
			->where("role_priviledge.role_id", \Auth::user()->role_id)
			->get();	
		$this->data["role_priviledges"] = $role_priviledges;
		return view('role.role_priviledges', $this->data);  	
	}

	public function postAddrolepriviledge(){
		$req = $this->data["req"];
		DB::table("role_priviledge")->where("role_id", $req->input("role_id"))->delete();		
		if ($req->input("ids_priviledge")!=null ){
			$allInsert =array();
			foreach ($req->input("ids_priviledge") as $key => $value) {
				$allInsert[] = array("role_id" => $req->input("role_id"), "priviledge_id" => $value);			
			}
			DB::table("role_priviledge")->insert($allInsert);
		}
		return Redirect::to('/role/edit/'.$req->input("role_id"));		
	}

	public function postUpdate(){
		$req = $this->data["req"];		
		DB::table("tb_role")
		->where("id", $req->input("role_id"))
		->update(["name" => $req->input("nama"), "description" => $req->input("description")]);
		return Redirect::to("/role/list");		
	}
	
}