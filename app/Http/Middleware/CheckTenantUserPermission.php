<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        if(is_null(Auth::user()->user_permissions)){
            abort('403', "You don't have permissions to acces this application");
        }
        
        $permissions = is_string(Auth::user()->user_permissions)
        ? json_decode(Auth::user()->user_permissions, true)
        : Auth::user()->user_permissions;

        if (in_array($request->route()->getName(), $permissions)) {
            $current_session_id = session('switched_tenant_id');
            if($current_session_id){
                tenancy()->initialize($current_session_id);
            }
            return $next($request);
        }

        abort(403, "Permission denied");
    }
}
