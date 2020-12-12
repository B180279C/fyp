<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staff;
use App\User;
use App\Student;
use Illuminate\Support\Facades\Storage;
use Image;

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

    public function deanDetails($user_id){

        $staff = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $image = $staff->staff_image;

        if($image == ""){
            return Image::make('/image/user.png')->response();
        }else{
            $storagePath = storage_path('/private/staffImage/' . $image);
            return Image::make($storagePath)->response();
        }
    }

    public function hodDetails($user_id){

        $staff = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $image = $staff->staff_image;

        if($image == ""){
            return Image::make('/image/user.png')->response();
        }else{
            $storagePath = storage_path('/private/staffImage/' . $image);
            return Image::make($storagePath)->response();
        }
    }

    public function lecturerDetails($user_id){

        $staff = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $image = $staff->staff_image;

        if($image == ""){
            return Image::make('/image/user.png')->response();
        }else{
            $storagePath = storage_path('/private/staffImage/' . $image);
            return Image::make($storagePath)->response();
        }
    }

    public function studentDetails($user_id){
        $student = Student::where('user_id', '=', $user_id)->firstOrFail();

        $image = $student->student_image;

        if($image == ""){
            return Image::make('/image/user.png')->response();
        }else{
            $storagePath = storage_path('/private/studentImage/' . $image);
            return Image::make($storagePath)->response();
        }
    }
}
