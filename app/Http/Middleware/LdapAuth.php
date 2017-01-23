<?php

namespace App\Http\Middleware;

use Closure,Session,Response;

class LdapAuth
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
        if (Session::has('ldap')){
            return $next($request);
        }
        
        if ($request->ajax()){
           $resopnseArray['session_expire'] = 'Your session is expired! Please sign in again';
           return Response::json($resopnseArray, 401)->header('Content-Type', 'application/json'); 
         }

        return redirect('/login');
    
    }
}
