<?php

namespace App\Http\Middleware;

use Closure;

class IsStaff
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
            if((auth()->user()->position == "Teacher")||(auth()->user()->position == "HoD")||(auth()->user()->position == "Dean")){
                return $next($request);
            }
        }
   
        return redirect()->route('login');
    }
}
