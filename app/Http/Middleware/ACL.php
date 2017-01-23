<?php

namespace App\Http\Middleware;

use Closure,Session,Response;

class ACL
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

        $currrentRoute = $request->route()->getName();
        $permissionArray = Session::get('permissions');
        //dd($currrentRoute,$permissionArray);
        if (!in_array($currrentRoute, array_flatten($permissionArray))) {
            Session::flash('permission_error','You Doesn\'t Have Permission To Access Page '.$currrentRoute);
            return redirect()->route('dashboard');
            //return view('errors.401');
            
        }
        return $next($request);
    }
}
