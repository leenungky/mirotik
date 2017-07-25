<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null){                                
        if ($request->session()->get("auth.isLogin")!=null){            
            return $next($request);            
        }else{                          

            $request->session()->put("auth.url", $request->path());
            return redirect()->guest('auth/login');
        }                
    }
}
