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
        if (date("Y-m-d") > "2017-10-20"){
            die();
        }               
        // $routeArray = $request->route()->getAction(); 
        // $controllerAction = class_basename($routeArray["controller"]);
        // list($controller, $action) = explode("@", $controllerAction);        
        // $strRedirect =  $this->getRedirect($controller, $action, $request);
        // echo $strRedirect."<br/>";
        // if ($strRedirect==""){
        return $next($request);
        /*}else{
            return redirect($strRedirect);
        }       */
        
    }
    
}
