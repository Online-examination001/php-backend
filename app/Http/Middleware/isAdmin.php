<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class isAdmin
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
        if (Auth::guest()::guest()  and !$request->is_admin){
            return response()->json(['error'=>'You are not authorised ','detal'=>'You have to be an admin']);
        }

        return $next($request);
    }
}
