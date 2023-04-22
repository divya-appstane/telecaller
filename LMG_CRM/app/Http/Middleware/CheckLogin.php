<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // var_dump(!Auth::guard('front')->check()); 
        // dd($request);
        // dd(Auth::guard('front')->user());
        // die;
        if(!Auth::guard('front')->check()) {
            return $next($request);
        }
        return redirect()->back();
    }
}
