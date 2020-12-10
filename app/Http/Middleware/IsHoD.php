<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\View;

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
                View::share('character','/hod');
                View::share('cha','Head Of Department');
                return $next($request);
            }
        }
   
        return redirect()->route('login');
    }
}
