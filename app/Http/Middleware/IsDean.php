<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\View;

use Closure;

class IsDean
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
            if(auth()->user()->position == "Dean"){
                View::share('character','');
                View::share('cha','Dean');
                return $next($request);
            }
        }
   
        return redirect()->route('login');
    }
}
