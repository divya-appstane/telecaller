<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Session;
use Redirect;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $session = Session::get("timer");
        
        if(isset($session) && !empty($session)) { 
            $redirecturl = Session::get("redirecturl");
            return redirect($redirecturl);
        }
        return $next($request);

    }
}
