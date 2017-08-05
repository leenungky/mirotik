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
        if ($strRedirect==""){
            return $next($request);
        }else{
            return redirect($strRedirect);
        }       
        
    }

    public function getRedirect($controller, $action, $request){
        $role =  $request->session()->get("role", ""); 
        $strRedirect = "";        
        if ($role=="staff"){
            if ($controller == "UserController"){
                if ($action == "getLogout" || $action=="getLogin" || $action=="postLogin"){                    
                }else{
                    $strRedirect = "/mikrotik/list";    
                }                
            } else{
                $strRedirect = "/mikrotik/list";    
            } 
            
            
        }        
        return $strRedirect;

    }
}
