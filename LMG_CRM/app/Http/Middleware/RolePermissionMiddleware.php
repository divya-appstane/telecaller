<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles = null, $permissions = null)
    {
        // $parameters = array_slice(func_get_args(), 2);
        $permissions = collect(explode('|',$permissions));

        
        
        if($roles != null){
            $roles = collect(explode('|',$roles));
            foreach($roles as $role){
                // dd(auth()->guard('front')->user());
                if(auth()->guard('front')->user()->hasRole($roles)) {
                    return $next($request);   
                }
            }

        }

        if($permissions != null){
            foreach($permissions as $permission){
                if($permission != null && auth()->guard('front')->user()->can($permission)) {
                    return $next($request);   
                }
            }

        }
        
        abort(403);
        
        return $next($request);
    }
}
