<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
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
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if(Auth()->user()->position == "admin"){
                return redirect()->route('admin.home');
            }else if(Auth()->user()->position == "Lecturer"){
                return redirect()->route('teacher.home');
            }else if(Auth()->user()->position == "Dean"){
                return redirect()->route('dean.home');
            }else if(Auth()->user()->position == "HoD"){
                return redirect()->route('hod.home');
            }else{
                return redirect()->route('home');
            }
        }
        return $next($request);
    }
}
