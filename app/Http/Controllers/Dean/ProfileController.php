<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\MatchOldPassword;
use Image;
use App\User;
use App\Staff;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class ProfileController extends Controller
{
	public function profile()
    {
        $user_id = auth()->user()->user_id;
        $staff   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user   = User::where('user_id', '=', $user_id)->firstOrFail();
        $faculty = Faculty::where('faculty_id', '=', $staff->faculty_id)->firstOrFail();
        $department = Department::where('department_id', '=', $staff->department_id)->firstOrFail();
        return view('profile',compact('staff','user','faculty','department'));
    }

    public function ProfileDownloadCV($id)
    {
        $staff = Staff::where('staff_id', '=', $id)->firstOrFail();
        $CV = $staff->lecturer_CV;
        $ext = "";
        if($staff->lecturer_CV!=""){
            $ext = explode(".", $staff->lecturer_CV);
        }
        return Storage::disk('private')->download('/staffCV/'.$CV,$id.'.'.$ext[1]);
    }

    public function profileImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $checkImageFaculty = Staff::where('staff_image', '=', $image_name)->firstOrFail();
        $image_user_id = $checkImageFaculty->user_id;
        if($user_id==$image_user_id){
            $storagePath = storage_path('/private/staffImage/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
        // $storagePath = storage_path('/private/staffImage/' . $image_name);
        // return Image::make($storagePath)->response();
    }

    public function profileSign($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $checkImageFaculty = Staff::where('staff_sign', '=', $image_name)->firstOrFail();
        $image_user_id = $checkImageFaculty->user_id;
        if($user_id==$image_user_id){
            $storagePath = storage_path('/private/staffSign/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
        // $storagePath = storage_path('/private/staffImage/' . $image_name);
        // return Image::make($storagePath)->response();
    }

    public function uploadImages(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/staff_Image/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyImage(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('/fake/staff_Image/'.$filename);
        return $filename;  
    }

    public function uploadCV(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/staff_CV/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);
    }

    public function destroyCV(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('/fake/staff_CV/'.$filename);
        return $filename;  
    }

    public function uploadSign(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/staff_Sign/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroySign(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('/fake/staff_Sign/'.$filename);
        return $filename;  
    }

    public function checkStaffID(Request $request)
    {
        $value = $request->get('value');

        $staff = Staff::where('staff_id', '=', $value)->first();
        if ($staff === null) {
            return "true";
        }else{
            return "false";
        }
    }

    public function store(Request $request)
    {
    	$id         = $request->get('id');
    	$staff_id   = $request->get('staff_id');
    	$email      = $request->get('staff_id')."@sc.edu.my";
    	$staff     = Staff::where('staff_id', '=', $id)->firstOrFail();
        $user      = User::find($staff->user_id);

        if($staff_id != $id){
        	$checkemail = User::where('email', '=', $email)->first();
            if ($checkemail === null) {
                $user->email = $email;
                $staff->staff_id = $staff_id;
            }else{
                if($request->get('staff_image')!=""){
                    Storage::disk('private')->delete('fake/staff_Image/'.$request->get('staff_image'));
                }
                if($request->get('staff_CV')!=""){
                    Storage::disk('private')->delete('fake/staff_CV/'.$request->get('staff_CV'));
                }
                if($request->get('staff_Sign')!=""){
                    Storage::disk('private')->delete('fake/staff_Sign/'.$request->get('staff_Sign'));
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
        		if($request->get('staff_image')!=""){
                    Storage::disk('private')->delete('fake/staff_Image/'.$request->get('staff_image'));
                }
                if($request->get('staff_CV')!=""){
                    Storage::disk('private')->delete('fake/staff_CV/'.$request->get('staff_CV'));
                }
                if($request->get('staff_Sign')!=""){
                    Storage::disk('private')->delete('fake/staff_Sign/'.$request->get('staff_Sign'));
                }
	            return redirect()->back()->withErrors($validator)->withInput();
	        }
	        $user->password = Hash::make($request['password']);
    	}

    	if($request->get('staff_image')!=""){
            $image_type = explode(".", $request->get('staff_image'));
            $staff->staff_image  = $request->get('staff_image');
            $image = Storage::disk('private')->get("fake/staff_Image/".$request->get('staff_image'));
            Storage::disk('private')->put('staffImage/'.$request->get('staff_image'), $image); 
            Storage::disk('private')->delete('fake/staff_Image/'.$request->get('staff_image'));
        }

        if($request->get('staff_CV')!=""){
            $CV_type = explode(".", $request->get('staff_CV'));
            $staff->lecturer_CV = $request->get('staff_CV');
            $CV = Storage::disk('private')->get("fake/staff_CV/".$request->get('staff_CV'));
            Storage::disk('private')->put('staffCV/'.$request->get('staff_CV'), $CV); 
            Storage::disk('private')->delete('fake/staff_CV/'.$request->get('staff_CV'));
        }

        if($request->get('staff_Sign')!=""){
            $image_type = explode(".", $request->get('staff_Sign'));
            $staff->staff_sign  = $request->get('staff_Sign');
            $image = Storage::disk('private')->get("fake/staff_Sign/".$request->get('staff_Sign'));
            Storage::disk('private')->put('staffSign/'.$request->get('staff_Sign'), $image); 
            Storage::disk('private')->delete('fake/staff_Sign/'.$request->get('staff_Sign'));
        }

    	$staff->save();
        $user->save();

        return redirect()->route('Profile')->with('success','Data Updated');
    }
}