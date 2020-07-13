<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\User;
use App\Department;
use App\Academic;

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
        $academic = Academic::all()->toArray();
        return view('admin.StaffCreate', compact('departments', 'academic'));
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
            'name'                  =>  'required',
            'staff_id'              =>  'required',
            'password'              =>  'min:8|confirmed|required',
            'password_confirmation' =>  'required',
            'position'              =>  'string',
            'academic'              =>  'string',
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
                'academic_id'     => $request->get('academic'),
            ]);
            
            $staff->save();
            return redirect()->route('admin.staff_list.index')->with('success','Data Added');
        }else{
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
        $academic = Academic::all()->toArray();
        return view('admin.StaffEdit', compact('staff', 'user' , 'departments' ,'academic', 'id'));
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
        $this->validate($request, [
            'name'                  =>  'required',
            'position'              =>  'string',
            'department'            =>  'string',
            'academic'              =>  'string',
        ]);
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
                return redirect()->back()->with('failed','The Email has been existed');
            }
        }
        $user->name             = $request->get('name');
        $user->position         = $request->get('position');

        $staff->department_id   = $request->get('department');
        $staff->academic_id     = $request->get('academic');
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


    public function staffAcademic(Request $request)
    {
        $value = $request->get('value');

        $departments = Department::all()->toArray();
        $academic = Academic::where('academic_id', '=', $value)->firstOrFail();
        $data = "";
        foreach($departments as $row){
            if($row['academic_id'] == $value){
                $department_id = $row['department_id'];
                $department_name = $row['department_name'];
                $data .= "<option value=$department_id>$department_name</option>";
            }
        }

        if($data==""){
            return "null";
        }

        $result = "<optgroup label='$academic->academic_name'>'".$data."'</optgroup>";
        return $result;
    }
}
