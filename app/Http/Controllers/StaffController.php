<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\User;
use App\Department;
use App\Faculty;

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

        return view('admin.staffIndex', ['staffs' => $staffs]);
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
            
            $image_type = explode(".", $request->get('staff_image'));
            $image_name = $request->get('name')."(".$request->get('staff_id').")_Image.".$image_type[1];
            $CV_type = explode(".", $request->get('staff_CV'));
            $CV_name = $request->get('name')."(".$request->get('staff_id').")_CV.".$CV_type[1];


            $staff = new Staff([
                'user_id'         => $user_id,
                'staff_id'        => $request->get('staff_id'),
                'department_id'   => $request->get('department'),
                'faculty_id'      => $request->get('faculty'),
                'staff_image'     => $image_name,
                'lecturer_CV'     => $CV_name,
            ]);
            $staff->save();

            $image = "fake/staff_Image/".$request->get('staff_image');
            $CV = "fake/staff_CV/".$request->get('staff_CV');
            rename($image, 'staffImage/'.$image_name);
            rename($CV, 'staffCV/'.$CV_name);
            return redirect()->route('admin.staff_list.index')->with('success','Data Added');
        }else{
            if($request->get('staff_image')!=""){
                $path1 = public_path().'/fake/staff_Image/'.$request->get('staff_image');
                unlink($path1);
            }
            if($request->get('staff_CV')!=""){
                $path2 = public_path().'/fake/staff_CV/'.$request->get('staff_CV');
                unlink($path2);
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
    public function show($id)
    {
        //
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
                    $path1 = public_path().'/fake/staff_Image/'.$request->get('staff_image');
                    unlink($path1);
                }
                if($request->get('staff_CV')!=""){
                    $path2 = public_path().'/fake/staff_CV/'.$request->get('staff_CV');
                    unlink($path2);
                }
                return redirect()->back()->with('failed','The Email has been existed');
            }
        }
        $user->name             = $request->get('name');
        $user->position         = $request->get('position');
        $staff->department_id   = $request->get('department');
        $staff->faculty_id      = $request->get('faculty');

        if($staff->staff_image!=""){
            $image = 'staffImage/'.$staff->staff_image;
            $image_type = explode(".", $image);
            $image_name = $request->get('name')."(".$staff_id.")_Image.".$image_type[1];
            $staff->staff_image  = $image_name;
            rename($image, 'staffImage/'.$image_name);
        }

        if($staff->lecturer_CV!=""){
            $CV = 'staffCV/'.$staff->lecturer_CV;
            $CV_type = explode(".", $CV);
            $CV_name = $request->get('name')."(".$staff_id.")_CV.".$CV_type[1];
            $staff->lecturer_CV = $CV_name;
            rename($CV, 'staffCV/'.$CV_name);
        }
        
        if($request->get('staff_image')!=""){
            $image_type = explode(".", $request->get('staff_image'));
            $image_name = $request->get('name')."(".$staff_id.")_Image.".$image_type[1];
            $staff->staff_image  = $image_name;
            $image = "fake/staff_Image/".$request->get('staff_image');
            rename($image, 'staffImage/'.$image_name);
        }

        if($request->get('staff_CV')!=""){
            $CV_type = explode(".", $request->get('staff_CV'));
            $CV_name = $request->get('name')."(".$staff_id.")_CV.".$CV_type[1];
            $CV = "fake/staff_CV/".$request->get('staff_CV');
            $staff->lecturer_CV     = $CV_name;
            rename($CV, 'staffCV/'.$CV_name);
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

        $path = public_path().'/staffImage/'.$image;
        if (file_exists($path)) {
            unlink($path);
        }
        return $image;
    }

    public function removeCV(Request $request)
    {
        $value = $request->get('value');
        $CV = $request->get('CV');

        $staff = Staff::where('staff_id', '=', $value)->firstOrFail();
        $staff->lecturer_CV = "";
        $staff->save();

        $path = public_path().'/staffCV/'.$CV;
        if (file_exists($path)) {
            unlink($path);
        }
        return $CV;
    }

    public function uploadImages(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('/fake/staff_Image/'),$imageName);
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyImage(Request $request)
    {
        $filename =  $request->get('filename');
        $path = public_path().'/fake/staff_Image/'.$filename;
        if (file_exists($path)) {
            unlink($path);
        }
        return $filename;  
    }

    public function uploadCV(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('/fake/staff_CV/'),$imageName);
        return response()->json(['success'=>$imageName]);
    }

    public function destroyCV(Request $request)
    {
        $filename =  $request->get('filename');
        $path = public_path().'/fake/staff_CV/'.$filename;
        if (file_exists($path)) {
            unlink($path);
        }
        return $filename;  
    }
}
