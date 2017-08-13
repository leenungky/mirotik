<?php

namespace App\Http\Middleware;

use Closure;

class BusinessLogic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {  
        $routeArray = $request->route()->getAction(); 
        $controllerAction = class_basename($routeArray["controller"]);
        list($controller, $action) = explode("@", $controllerAction);        
        $strRedirect =  $this->getRedirect($controller, $action, $request);
        echo $strRedirect."<br/>";
        if ($strRedirect==""){

            return $next($request);
        }else{
            return redirect($strRedirect);
        }       
        
    }

    public function getRedirect($controller, $action, $request){
        $role =  $request->session()->get("role", ""); 
        $strRedirect = "";                
        if ($role=="FO"){
            if ($controller == "UserController"){
                if ($action == "getLogout" || $action=="getLogin" || $action=="postLogin"){                    
                }else{
                    $strRedirect = "/customer/list";    
                }                
            } else{
                $strRedirect = "/customer/list";    
            }             
            
        }   
        if (empty($role)){   
            if ($controller == "UserController"){
                if ($action == "getLogout" || $action=="getLogin" || $action=="postLogin"){  
                    $is_cek_payment = false;
                }
            }else{                 
                $strRedirect = "/user/logout";                                     
            }
        }
        return $strRedirect;

    }
}
