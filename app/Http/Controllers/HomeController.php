<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staff;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function adminHome()
    {
        return view('adminHome');
    }

    // public function staffHome()
    // {
    //     return view('staffHome');
    // }

    public function teacherHome()
    {
        return view('teacherHome');
    }
    public function hodHome()
    {
        return view('hodHome');
    }
    public function deanHome()
    {
        return view('deanHome');
    }

    public function deanDetails(Request $request){
        $value = $request->get('value');
        $staff = Staff::where('user_id', '=', $value)->firstOrFail();

        $image = $staff->staff_image;
        if($image == ""){
            return "null";
        }else{
            return $image;
        }
    }
}
