<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\View;

use Closure;

class IsTeacher
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
            if(auth()->user()->position == "Lecturer"){
                View::share('character','/lecturer');
                return $next($request);
            }
        }
   
        return redirect()->route('login');
    }
}
