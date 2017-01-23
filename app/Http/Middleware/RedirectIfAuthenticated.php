<?php

namespace App\Http\Middleware;

use Closure,Session;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('ldap')){
            return redirect('/dashboard');
        }
        return $next($request);
    }
}
