<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\User;
use App\Department;
use App\Faculty;
use App\Exports\StaffExport;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->join('departments', 'staffs.department_id', '=', 'departments.department_id')
                    ->select('staffs.*', 'users.email', 'users.name','users.position', 'departments.department_name')
                    ->orderBy('staffs.id')
                    ->get();

        return view('admin.StaffIndex', ['staffs' => $staffs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all()->toArray();
        $faculty = Faculty::all()->toArray();
        return view('admin.StaffCreate', compact('departments', 'faculty'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'password'              =>  'min:8|confirmed|required',
        ]);

        $email = $request->get('staff_id')."@sc.edu.my";
        $checkemail = User::where('email', '=', $email)->first();

        if ($checkemail === null) {
            $user = new User([
                'name'              => $request->get('name'),
                'email'             => $email,
                'password'          => Hash::make($request['password']),
                'status'            => 'Not Active',
                'position'          => $request->get('position'),
            ]);
            $user->save();
            $user_id = $user->user_id;

            $staff = new Staff([
                'user_id'         => $user_id,
                'staff_id'        => $request->get('staff_id'),
                'department_id'   => $request->get('department'),
                'faculty_id'      => $request->get('faculty'),
                'staff_image'     => $request->get('staff_image'),
                'lecturer_CV'     => $request->get('staff_CV'),
            ]);
            $staff->save();

            if($request->get('staff_image')!=""){
                $image = Storage::disk('private')->get("fake/staff_Image/".$request->get('staff_image'));
                Storage::disk('private')->put('staffImage/'.$request->get('staff_image'), $image); 
                Storage::disk('private')->delete('fake/staff_Image/'.$request->get('staff_image'));
            }

            if($request->get('staff_CV')!=""){
                $CV = Storage::disk('private')->get("fake/staff_CV/".$request->get('staff_CV'));
                Storage::disk('private')->put('staffCV/'.$request->get('staff_CV'), $CV);
                Storage::disk('private')->delete('fake/staff_CV/'.$request->get('staff_CV'));
            }
            return redirect()->route('admin.staff_list.index')->with('success','Data Added');
        }else{
            if($request->get('staff_image')!=""){
                Storage::disk('private')->delete('fake/staff_Image/'.$request->get('staff_image'));
            }
            if($request->get('staff_CV')!=""){
                Storage::disk('private')->delete('fake/staff_CV/'.$request->get('staff_CV'));
            }
            return redirect()->route('staff.create')->with('failed','The Email has been existed');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($image_name)
    {
        $storagePath = storage_path('/private/staffImage/' . $image_name);
        return Image::make($storagePath)->response();
    }


    public function downloadCV($id)
    {
        $staff = Staff::where('staff_id', '=', $id)->firstOrFail();
        $CV = $staff->lecturer_CV;
        $ext = "";
        if($staff->lecturer_CV!=""){
            $ext = explode(".", $staff->lecturer_CV);
        }
        return Storage::disk('private')->download('/staffCV/'.$CV,$id.'.'.$ext[1]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = Staff::where('staff_id', '=', $id)->firstOrFail();
        $user = User::find($staff->user_id);
        $departments = Department::all()->toArray();
        $faculty = Faculty::all()->toArray();
        return view('admin.StaffEdit', compact('staff', 'user' , 'departments' ,'faculty', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $staff_id  = $request->get('staff_id');
        $email     = $request->get('staff_id')."@sc.edu.my";
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
                return redirect()->back()->with('failed','The Email has been existed');
            }
        }

        $user->name             = $request->get('name');
        $user->position         = $request->get('position');
        $staff->department_id   = $request->get('department');
        $staff->faculty_id      = $request->get('faculty');
        
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

        $staff->save();
        $user->save();

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.staff_list.index')->with('success','Data Updated');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function staffFaculty(Request $request)
    {
        $value = $request->get('value');

        $departments = Department::all()->toArray();
        $faculty = Faculty::where('faculty_id', '=', $value)->firstOrFail();
        $data = "";
        foreach($departments as $row){
            if($row['faculty_id'] == $value){
                $department_id = $row['department_id'];
                $department_name = $row['department_name'];
                $data .= "<option value=$department_id class='option'>$department_name</option>";
            }
        }

        if($data==""){
            return "null";
        }

        $result = "<optgroup label='$faculty->faculty_name'>'".$data."'</optgroup>";
        return $result;
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

    public function removeImage(Request $request)
    {
        $value = $request->get('value');
        $image = $request->get('image');

        $staff = Staff::where('staff_id', '=', $value)->firstOrFail();
        $staff->staff_image = "";
        $staff->save();

        Storage::disk('private')->delete('/staffImage/'.$image);
        return $image;
    }

    public function removeCV(Request $request)
    {
        $value = $request->get('value');
        $CV = $request->get('CV');

        $staff = Staff::where('staff_id', '=', $value)->firstOrFail();
        $staff->lecturer_CV = "";
        $staff->save();

        Storage::disk('private')->delete('/staffCV/'.$CV);
        return $CV;
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

    public function downloadExcel()
    {
        return Excel::download(new StaffExport, 'Staff.xlsx');
    }
}
