<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;   
use App\Http\Helpers\Api;
use App\Http\Helpers\Helpdesk; 

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    // public function login(Request $req){
    //     $email = $req->input("email");
    //     $phone = $req->input("email");
    //     $password = $req->input("password");
    //     $json = Api::getLogin($email, $phone, $password);  
    //     $decode = json_decode($json);
    //     if ($decode->response->code==200){            
    //         $req->session()->put('user', $email);                  
    //         $req->session()->put('uid_token', $decode->response->result->uid_token);
    //         return back()->with(['page_code' => 0, 'err_message' => $decode->response->message]);      
    //         return redirect()->route('landing-page');    
    //     }else{
    //         return back()->with(['page_code' => 0, 'err_message' => $decode->response->message]);            
    //     }
    // }

    public function join(Request $req){      
        $data["category"] = Api::getListCategory();
        $categories = Helpdesk::getLandingPage($data["category"]);
        $data["cookie"] = $req->query('cookie');
        $data["categories"] = $categories;
        $data["categoryName"] = "";  
        return view('auth/join', $data);
    }

    public function verification(Request $request){              
        $phone = $request->input("phone");         
        // If request method is post
        if($request->isMethod('post')){ #if post then process from form join
            // Get Phone Number

            $phone = $request->input('phone');
            // Validate Phone Number
            if(!preg_match('/^\(?\+?([0-9]{1,4})\)?[-\. ]?(\d{3})[-\. ]?([0-9]{5})$/', trim($phone))) {
                return back()->withInput()->with('error', 'Invalid Phone Number');
            }

            // Check existing phone using member detail
            $member = json_decode(Api::memberDetail($phone));
            if ($member->response->code!=200) {
                return back()->withInput()->with('error', 'Failed to Register');
            }
            if (!empty($member->data)) {
                return back()->withInput()->with('error', 'User already exists');
            }

            // Send Request to API
            $result = json_decode(Api::setOtp($phone));            
            if ($result->response->code!=200) {
                return back()->withInput()->with('error', 'Failed to Register');
            }            
           
            $data['phone'] = $phone;            
            $data["category"] = Api::getListCategory();
            $categories = Helpdesk::getLandingPage($data["category"]);
            $data["cookie"] = $request->query('cookie');
            $data["categories"] = $categories;
            $data["categoryName"] = "";
            $request->session()->put('auth.phone',$phone);
            return view('auth.verification',$data);
        } else {
            if (!$request->session()->has('auth.phone')) {
                return redirect('auth/join');
            }
            $data['phone'] = $request->session()->get('auth.phone');            
            return view('auth.verification',$data);
        }
    }

    public function postRegister(Request $request) {        
        if (!$request->session()->has('auth.phone')) {
            return redirect('/auth/join');
        }
        $data = array();
        if($request->isMethod('post')){
            
            $this->validate($request, [
                'code' => 'required',
            ]);            
            // Get Code user input            
            $code = $request->input('code');            
            $phone = $request->session()->get('auth.phone');            
            $result = json_decode(Api::otpValidation($phone,$code));                                                          
            if ($result->response->code!=200) {
                // If invalid PIN, send OTP again to same number
                $result = json_decode(Api::postOtpRegister($phone));                
                if (empty($result) || $result->response->code!=200) {                    
                    return redirect('/auth/join')->with('error', 'Failed to Register');
                }                 
                
                self::getParameter($data, $request);          
                $data['phone'] = $phone;      
                \Session::flash('error', 'otp not valid'); 
                return view('auth.verification',$data);                              
            }            
        } else {
            $phone = $request->session()->get('auth.phone');
        }        
        self::getParameter($data, $request);
        $data['phone'] = $phone;        
        return view('auth.registration',$data);
    }

    public function postLogin(Request $request){    
        
        $this->validate($request, [
            'phone' => 'required',
            'password' => 'required|min:5',
        ]);
        $phone = $request->input('phone');
        $password = $request->input('password');

        $login = json_decode(Api::memberLogin($phone, $password));

        if (empty($login)) {
            return back()->withInput()->with('error','Login Failed');
        }

        if ($login->response->code!=200) {
            return redirect('auth/login')->withInput()->with('error', $login->response->message);
        }
        $url = url('/');
        if ($request->session()->has('auth.url')) {
            $url = $request->session()->get('auth.url');
        }
        // Get Member Detail
        $memberDetail = json_decode(Api::memberDetail($phone));
        $memberDetail = $memberDetail->data[0];        
        $request->session()->put('auth.member',$memberDetail);
        $request->session()->put('auth.phone',$phone);        
        $request->session()->put('auth.uid_token',$login->response->result->uid_token);
        $request->session()->put('auth.isLogin',true);
        return redirect($url);
    }

    public function forgot(Request $req){
        $data = array();
        self::getParameter($data, $req);
        return view('auth.forgot',$data);
    }

    public function postForgot(Request $request){
        $this->validate($request, [
            'phone'=>'required'
        ]);

        $phone = $request->input('phone');
        $reset = json_decode(Api::createPassword($phone));
        if (empty($reset)) {
            return back()->withInput();
        }
        if ($reset->response->code!=200) {
            $request->session()->flash('error', $reset->response->message);
            return back()->withInput();
        }
        return redirect('auth/login');
    }

    public function complete(Request $request){
        if (!$request->session()->has('auth.phone')) {
            return redirect('/auth/join');
        }
        if ($request->isMethod('post')) {
            // dd($request->input());
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:5',
                'repassword' => 'required|same:password'
            ]);
            $phone = $request->session()->get('auth.phone');
            // $createPass = Helpdesk::createPassword($phone);
            // if ($createPass->response->code!=200) {
            //     return back()->withInput()->with('error', 'Failed to Complete');
            // }
            $password = $request->input('password');            
            $updatePass = json_decode(Api::updatePassword($phone, $password));            
            if ($updatePass->response->code!=200) {
                return back()->withInput()->with('error', 'Failed to Complete');
            }

            $name = $request->input('name');
            $email = $request->input('email');
            $updateMember = json_decode(Api::updateData($phone, $email, $name));
            if ($updateMember->response->code!=200) {
                return back()->withInput()->with('error', 'Failed to Complete');
            }
        }
        $url = url('/');
        if ($request->session()->has('auth.url')) {
            $url = $request->session()->get('auth.url');
        }

        $request->session()->put('auth.isLogin',true);
        // Get Member Detail
        $phone = $request->session()->get('auth.phone');
        $memberDetail = json_decode(Api::memberDetail($phone));
        $memberDetail = $memberDetail->data[0];

        $request->session()->put('auth.member',$memberDetail);
        
        $data['url'] = $url;                
        self::getParameter($data, $request);
        return redirect()->route('landing-page');
        // return view('auth.complete',$data);
    }    

    public function showLoginForm(Request $req){    
        $data["category"] = Api::getListCategory();
        $categories = Helpdesk::getLandingPage($data["category"]);
        $data["cookie"] = $req->query('cookie');
        $data["categories"] = $categories;
        $data["categoryName"] = "";
        return view('auth/login', $data);
    }

    public function logout(Request $req){
        $req->session()->forget('auth.isLogin');
        $req->session()->forget('auth.phone');
        return redirect()->route('landing-page');
    }

    private function getParameter(&$data, $req){
        $data["category"] = Api::getListCategory();
        $categories = Helpdesk::getLandingPage($data["category"]);
        $data["cookie"] = $req->query('cookie');
        $data["categories"] = $categories;
        $data["categoryName"] = "";
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
