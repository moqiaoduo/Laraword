<?php

namespace App\Http\Middleware;

use Closure;
use Storage;

class AdminAuth
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
        if(!$request->user()) return redirect()->guest('login');

        if($request->user() && $request->user()->isAdmin()){
            return $next($request);
        }

        if ($request->ajax()) {
            return response('Unauthorized.', 401);
        } else {
            return redirect()->guest('/');
        }
    }
}
