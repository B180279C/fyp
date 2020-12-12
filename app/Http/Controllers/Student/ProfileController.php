<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\MatchOldPassword;
use Image;
use App\User;
use App\Student;
use App\Staff;
use App\Subject;
use App\Semester;
use App\Programme;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class ProfileController extends Controller
{
	public function profile()
    {
        $user_id = auth()->user()->user_id;
        $student = Student::where('user_id', '=', $user_id)->firstOrFail();
        // $staff   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user   = User::where('user_id', '=', $user_id)->firstOrFail();
        $programme = Programme::where('programme_id', '=', $student->programme_id)->firstOrFail();
        $semester = Semester::where('semester_id', '=', $student->semester)->firstOrFail();
        return view('student_profile',compact('student','user','programme','semester'));
    }

    public function profileImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $checkImageFaculty = Student::where('student_image', '=', $image_name)->firstOrFail();
        $image_user_id = $checkImageFaculty->user_id;
        if($user_id==$image_user_id){
            $storagePath = storage_path('/private/studentImage/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function uploadImages(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/student_Image/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyImage(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('/fake/student_Image/'.$filename);
        return $filename;  
    }

    public function store(Request $request)
    {
    	$id         = $request->get('id');
    	$student_id   = $request->get('student_id');
    	$email      = $request->get('student_id')."@sc.edu.my";
    	$student     = Student::where('student_id', '=', $id)->firstOrFail();
        $user      = User::find($student->user_id);

        if($student_id != $id){
        	$checkemail = User::where('email', '=', $email)->first();
            if ($checkemail === null) {
                $user->email = $email;
                $student->student_id = $student_id;
            }else{
                if($request->get('student_image')!=""){
                    Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
                }
                return redirect()->back()->with('failed','The Email has been existed');
            }
        }

        $user->name       = $request->get('name');
    	$password         = $request->get('password');
    	$current_password = Hash::make($request['current']);

    	if($password!=""){
    		$validator = Validator::make($request->all(), [
    			'current_password'      => ['required', new MatchOldPassword],
            	'password'              =>  'min:8|confirmed|required',
        	]);
        	if($validator->fails()) {
        		if($request->get('student_image')!=""){
                    Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
                }
	            return redirect()->back()->withErrors($validator)->withInput();
	        }
	        $user->password = Hash::make($request['password']);
    	}

    	if($request->get('student_image')!=""){
            $image_type = explode(".", $request->get('student_image'));
            $student->student_image  = $request->get('student_image');
            $image = Storage::disk('private')->get("fake/student_Image/".$request->get('student_image'));
            Storage::disk('private')->put('studentImage/'.$request->get('student_image'), $image); 
            Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
        }
    	$student->save();
        $user->save();
        return redirect()->back()->with('success','Data Updated');
    }
}