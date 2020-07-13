<?php

namespace App\Http\Middleware;

use Closure;

class IsHoD
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
        if(auth()->user()){
            if(auth()->user()->position == "HoD"){
                return $next($request);
            }
        }
   
        return redirect()->route('login');
    }
}
