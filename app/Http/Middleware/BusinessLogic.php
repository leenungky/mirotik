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
        $strRedirect =  $this->getRedirect($controller, $action, $request->session()->get("role"));
        if ($strRedirect==""){
            return $next($request);
        }else{
            return redirect($strRedirect);
        }       
        
    }

    public function getRedirect($controller, $action, $role){
        $strRedirect = "";        
        if ($role=="staff"){
            if ($controller == "UserController"){
                if ($action == "getLogout" || $action=="getLogin" || $action=="postLogin"){                    
                }else{
                    $strRedirect = "/transaction";    
                }
                
            }else if ($controller == "CustomerController" 
                || $controller == "ReportController" 
                || $controller == "CityController"
                || $controller == "RoleController"
                || $controller == "CollectController"
                || $controller == "PriceController"
                || $controller == "TreeplController"
                || $controller == "EmployeeController" ){
                $strRedirect = "/transaction";
            }
        }
        if ($role=="admin"){
             if ($controller == "UserController"){
                if ($action == "getLogout" || $action=="getLogin" || $action=="postLogin"){  
                }else{
                    $strRedirect = "/customer";    
                }
            }
                
        }
        return $strRedirect;

    }
}
