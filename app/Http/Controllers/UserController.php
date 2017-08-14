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
use \URL;

class UserController extends Controller {

	
	protected $layout = "layouts.main";

	public function __construct(Request $req) {
		$this->data["type"]= "User"; 
		$this->data["req"]= $req; 
		$this->data["role"] =  $req->session()->get("role", ""); 
	} 

	public function getAdd(){
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}	
		$req = $this->data["req"];
		$role = DB::table("tb_role")->get();						
		$this->data["role"] = $role;
		return view('user.new', $this->data);  
	}
	public function getList(){
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];
		$input= $req->input();         		
		$dbuser = $this->_get_index_filter($input);     
		$this->data["input"] = $input;
		$this->data["users"] = $dbuser->get();
		return view('user.list', $this->data);  
	}

	public function getEdit($id){		
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];
		$user = DB::table("tb_users")->where("id" , $id)->first();	
		$role = DB::table("tb_role")->get();										
		$this->data["role"] = $role;
		$this->data["user"] = $user;
		$this->data["req"] = $req;
		return view('user.edit', $this->data);  
	}

	public function getDelete($id){		
		if ($this->data["role"]!="administrator"){
			return redirect('/customer/list');
		}
		$req = $this->data["req"];
		$user = DB::table("tb_users")->where("id" , $id)->delete();
		return redirect('/user/list')->with('message', "Successfull delete");
	}

	public function postUpdate($id){	
		$req = $this->data["req"];
		$rules = array(
			'firstname'=>'required|alpha_num|min:2',
			'lastname'=>'required|alpha_num|min:2',			
			'role' => 'required'			
			);	
		
		
		if (!empty($req->input("password"))){
			$rules["password"] = 'required|between:6,12|confirmed';	
			$rules['password_confirmation'] ='required|between:6,12';
		}

		$validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }

        $input = $req->input();        			
		$arrUpdate = array(
			"first_name" => $input["firstname"],
			"last_name" => $input["lastname"],
			"role_id" => $input["role"]
			);        
		if (!empty($input["password"])){
			$arrUpdate["password"] = \Hash::make($input["password"]);
		}		
        DB::table("tb_users")->where("id", $id)->update($arrUpdate);        
		return redirect('/user/list')->with('message', "Successfull delete");
	}

	// public function postCreated(Request $req){
	// 	print_r($req->input());
	// 	die();
	// }
	// batas bawah

	public function getRegister() {        
			if(\Auth::check()):
				 return Redirect::to('')->with('message',\SiteHelpers::alert('success','Youre already login'));
			else:
				 return Redirect::to('user/login');			  	
		 endif ; 
	}

	public function postCreate( Request $request) {	

		$rules = array(
			'firstname'=>'required|alpha_num|min:2',
			'lastname'=>'required|alpha_num|min:2',
			'email'=>'required|email|unique:tb_users',
			'role' => 'required',
			'password'=>'required|between:6,12|confirmed',
			'password_confirmation'=>'required|between:6,12'
			);	
		if ($request->input("role")=="3"){
			$rules["agent"] = "required";
		}

		$validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {            
            return Redirect::to(URL::previous())->withInput(Input::all())->withErrors($validator);            
        }        
		$authen = new User;
		$authen->first_name = $request->input('firstname');
		$authen->last_name = $request->input('lastname');
		$authen->role_id = $request->input('role');
		$authen->email = trim($request->input('email'));			
		$authen->password = \Hash::make($request->input('password'));
		$authen->save();
		return Redirect::to('user/list')->with('message',\SiteHelpers::alert('success',"Successfull created"));
		
	}
	
	public function getActivation()
	{
		$req = $this->data["req"];
		$num = $request->input('code');
		if($num =='')
			return Redirect::to('user/login')->with('message',\SiteHelpers::alert('error','Invalid Code Activation!'));
		
		$user =  User::where('activation','=',$num)->get();
		if (count($user) >=1)
		{
			\DB::table('tb_users')->where('activation', $num )->update(array('active' => 1,'activation'=>''));
			return Redirect::to('user/login')->with('message',\SiteHelpers::alert('success','Your account is active now!'));			
		} else {
			return Redirect::to('user/login')->with('message',\SiteHelpers::alert('error','Invalid Code Activation!'));
		}
	}

	public function getLogin() {
		$req = $this->data["req"]; 
		$role = $req->session()->get("role", "");
		if(empty($role)){
			$this->data['socialize'] =  config('services');
			return View('auth.login',$this->data);			
		} else {
			return Redirect::to('/customer/list')->with('message','Youre already login');			
		}	
	}

	public function postSignin() {
		$request = $this->data["req"];
		$rules = array(
			'email'=>'required|email',
			'password'=>'required',
		);		
		
		$validator = Validator::make(Input::all(), $rules);		
		if ($validator->passes()) {				

			$remember = (!is_null($request->get('remember')) ? 'true' : 'false' );				
			if (\Auth::attempt(array('email'=>$request->input('email'), 'password'=> $request->input('password') ), $remember )) {	

				if(\Auth::check())	
				{	

					$row = User::find(\Auth::user()->id); 

					if($row->active =='0')
					{
						// inactive 
						if($request->ajax() == true )
						{
							return response()->json(['status' => 'error', 'message' => 'Your Account is not active']);
						} else {
							\Auth::logout();
							return Redirect::to('user/login')->with('message', SiteHelpers::alert('error','Your Account is not active'));
						}
						
					} else if($row->active=='2')
					{

						if($request->ajax() == true )
						{
							return response()->json(['status' => 'error', 'message' => 'Your Account is BLocked']);
						} else {
							// BLocked users
							\Auth::logout();
							return Redirect::to('user/login')->with('message', SiteHelpers::alert('error','Your Account is BLocked'));
						}
					} else {

						\DB::table('tb_users')->where('id', '=',$row->id )->update(array('last_login' => date("Y-m-d H:i:s")));
						\Session::put('uid', $row->id);
						\Session::put('gid', $row->group_id);
						\Session::put('eid', $row->email);
						\Session::put('ll', $row->last_login);
						\Session::put('fid', $row->first_name.' '. $row->last_name);		
						$role = DB::table("tb_role")->where("id", $row->role_id)->first();
						\Session::put('role', $role->name);

						if($request->ajax() == true ){							
							return response()->json(['status' => 'success', 'url' => url('')]);
						} else {							
							return Redirect::to('customer/list');
							
						}					
					}		
					
				}						
			} else {

				if($request->ajax() == true ){
					return response()->json(['status' => 'error', 'message' => 'Your username/password combination was incorrect']);
				} else {
					return Redirect::to('')
						->with('message', 'Your username/password combination was incorrect')
						->withInput();					
				}
			}
		} else {
				if($request->ajax() == true)
				{
					return response()->json(['status' => 'error', 'message' => 'The following  errors occurred']);
				} else {

					return Redirect::to('user/login')
						->with('message','The following  errors occurred')
						->withErrors($validator)->withInput();
				}	
		}	
	}

	public function getProfile() {
		
		if(!\Auth::check()) return redirect('user/login');
		
		
		$info =	User::find(\Auth::user()->id);
		$this->data = array(
			'pageTitle'	=> 'My Profile',
			'pageNote'	=> 'View Detail My Info',
			'info'		=> $info,
		);
		return view('user.profile',$this->data);
	}
	
	public function postSaveprofile( Request $request)
	{
		if(!\Auth::check()) return Redirect::to('user/login');
		$rules = array(
			'first_name'=>'required|alpha_num|min:2',
			'last_name'=>'required|alpha_num|min:2',
			);	
			
		if($request->input('email') != \Session::get('eid'))
		{
			$rules['email'] = 'required|email|unique:tb_users';
		}	

		if(!is_null(Input::file('avatar'))) $rules['avatar'] = 'mimes:jpg,jpeg,png,gif,bmp';

				
		$validator = Validator::make($request->all(), $rules);

		if ($validator->passes()) {
			
			
			if(!is_null(Input::file('avatar')))
			{
				$file = $request->file('avatar'); 
				$destinationPath = './uploads/users/';
				$filename = $file->getClientOriginalName();
				$extension = $file->getClientOriginalExtension(); //if you need extension of the file
				 $newfilename = \Session::get('uid').'.'.$extension;
				$uploadSuccess = $request->file('avatar')->move($destinationPath, $newfilename);				 
				if( $uploadSuccess ) {
				    $data['avatar'] = $newfilename; 
				} 
				
			}		
			
			$user = User::find(\Session::get('uid'));
			$user->first_name 	= $request->input('first_name');
			$user->last_name 	= $request->input('last_name');
			$user->email 		= $request->input('email');
			if(isset( $data['avatar']))  $user->avatar  = $newfilename; 			
			$user->save();

			return Redirect::to('user/profile')->with('messagetext','Profile has been saved!')->with('msgstatus','success');
		} else {
			return Redirect::to('user/profile')->with('messagetext','The following errors occurred')->with('msgstatus','error')
			->withErrors($validator)->withInput();
		}	
	
	}
	
	public function postSavepassword( Request $request)
	{
		$rules = array(
			'password'=>'required|between:6,12',
			'password_confirmation'=>'required|between:6,12'
			);		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$user = User::find(\Session::get('uid'));
			$user->password = \Hash::make($request->input('password'));
			$user->save();

			return Redirect::to('user/profile')->with('message', \SiteHelpers::alert('success','Password has been saved!'));
		} else {
			return Redirect::to('user/profile')->with('message', \SiteHelpers::alert('error','The following errors occurred')
			)->withErrors($validator)->withInput();
		}	
	
	}	
	
	public function getReminder()
	{
	
		return view('user.remind');
	}	

	public function postRequest( Request $request)
	{

		$rules = array(
			'credit_email'=>'required|email'
		);	
		
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->passes()) {	
	
			$user =  User::where('email','=',$request->input('credit_email'));
			if($user->count() >=1)
			{
				$user = $user->get();
				$user = $user[0];
				$data = array('token'=>$request->input('_token'));	
				$to = $request->input('credit_email');
				$subject = "[ " .CNF_APPNAME." ] REQUEST PASSWORD RESET "; 	

				if(defined('CNF_MAIL') && CNF_MAIL =='swift')
				{ 
					Mail::send('user.emails.auth.reminder', $data, function ($message) {
			    		$message->to($to)->subject($subject);
			    	});	

				}  else {

							
					$message = view('user.emails.auth.reminder', $data);
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.CNF_APPNAME.' <'.CNF_EMAIL.'>' . "\r\n";
						mail($to, $subject, $message, $headers);	
				}					
			
				
				$affectedRows = User::where('email', '=',$user->email)
								->update(array('reminder' => $request->input('_token')));
								
				return Redirect::to('user/login')->with('message', SiteHelpers::alert('success','Please check your email'));	
				
			} else {
				return Redirect::to('user/login?reset')->with('message', SiteHelpers::alert('error','Cant find email address'));
			}

		}  else {
			return Redirect::to('user/login?reset')->with('message', 'The following errors occurred'
			)->withErrors($validator)->withInput();
		}	 
	}	
	
	public function getReset( $token = '')
	{
		if(\Auth::check()) return Redirect::to('dashboard');

		$user = User::where('reminder','=',$token);
		if($user->count() >=1)
		{
			$this->data['verCode']= $token;
			return view('user.remind',$this->data);

		} else {
			return Redirect::to('user/login')->with('message', \SiteHelpers::alert('error','Cant find your reset code'));
		}
		
	}	
	
	public function postDoreset( Request $request , $token = '')
	{
		$rules = array(
			'password'=>'required|alpha_num|between:6,12|confirmed',
			'password_confirmation'=>'required|alpha_num|between:6,12'
			);		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			
			$user =  User::where('reminder','=',$token);
			if($user->count() >=1)
			{
				$data = $user->get();
				$user = User::find($data[0]->id);
				$user->reminder = '';
				$user->password = \Hash::make($request->input('password'));
				$user->save();
			}
			return Redirect::to('user/login')->with('message',\SiteHelpers::alert('success','Password has been saved!'));
		} else {
			return Redirect::to('user/reset/'.$token)->with('message', \SiteHelpers::alert('error','The following errors occurred')
			)->withErrors($validator)->withInput();
		}	
	
	}	

	public function getLogout() {
		$currentLang = \Session::get('lang');
		\Auth::logout();
		\Session::flush();
		\Session::put('lang', $currentLang);
		return Redirect::to('')->with('message', 'Your are now logged out!');
	}

	function getSocialize( $social )
	{
		return Socialize::with($social)->redirect();
	}

	function getAutosocial( $social )
	{
		$user = Socialize::with($social)->user();
		$user =  User::where('email',$user->email)->first();
		return self::autoSignin($user);		
	}


	function autoSignin($user)
	{

		if(is_null($user)){
		  return Redirect::to('user/login')
				->with('message', \SiteHelpers::alert('error','You have not registered yet '))
				->withInput();
		} else{

		    Auth::login($user);
			if(Auth::check())
			{
				$row = User::find(\Auth::user()->id); 

				if($row->active =='0')
				{
					// inactive 
					Auth::logout();
					return Redirect::to('user/login')->with('message', \SiteHelpers::alert('error','Your Account is not active'));

				} else if($row->active=='2')
				{
					// BLocked users
					Auth::logout();
					return Redirect::to('user/login')->with('message', \SiteHelpers::alert('error','Your Account is BLocked'));
				} else {
					Session::put('uid', $row->id);
					Session::put('gid', $row->group_id);
					Session::put('eid', $row->group_email);
					Session::put('fid', $row->first_name.' '. $row->last_name);	
					if(CNF_FRONT =='false') :
						return Redirect::to('dashboard');						
					else :
						return Redirect::to('');
					endif;					
					
										
				}
				
				
			}
		}

	}

	//================= batas
	private function _get_index_filter($filter){
        $dbuser = DB::table("tb_users")
        ->select("tb_users.id", "tb_users.first_name", "tb_users.last_name", "tb_users.email", "tb_role.name as role")
        ->join("tb_role", "tb_role.id", "=", "tb_users.role_id", "left");        
        if (isset($filter["email"])){
            $dbuser = $dbuser->where("email", "like", "%".$filter["email"]."%");
        }        
        return $dbuser;
    }
	
}