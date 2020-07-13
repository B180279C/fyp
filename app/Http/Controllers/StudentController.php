<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Student;
use App\User;
use App\Programme;
use App\Faculty;
use App\Department;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = DB::table('students')
                    ->join('users', 'students.user_id', '=', 'users.user_id')
                    ->join('programmes','students.programme_id', '=', 'programmes.programme_id')
                    ->select('students.*', 'users.email', 'users.name', 'programmes.programme_name','programmes.short_form_name')
                    ->get();
        return view('admin.studentIndex', ['students' => $students]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->orderBy('programme_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        return view('student.StudentCreate', compact('programme','faculty'));
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
            'student_id'            =>  'required',
            'password'              =>  'min:8|confirmed|required',
            'password_confirmation' =>  'required',
            'programme'             =>  'string',
            'year'                  =>  'string',
            'semester'              =>  'string',
            'intake'                =>  'string',
        ]);

        $email = $request->get('student_id')."@sc.edu.my"; 

        $checkemail = User::where('email', '=', $email)->first();

        if ($checkemail === null) {
            $user = new User([
            'name'              => $request->get('name'),
            'email'             => $email,
            'password'          => Hash::make($request['password']),
            'status'            => 'Not Active', 
            'position'          => 'student',
            ]);
            $user->save();
            $user_id = $user->user_id;

            $student = new Student([
                'user_id'           => $user_id,
                'student_id'        => $request->get('student_id'),
                'programme_id'      => $request->get('programme'),
                'year'              => $request->get('year'),
                'semester'          => $request->get('semester'),
                'intake'            => $request->get('intake'),
            ]);
            $student->save();

            if(auth()->user()){
                if(auth()->user()->position == "admin"){
                    return redirect()->route('admin.student_list.index')->with('success','Data Added');
                }
            }
            else{
                return redirect()->route('login')->with('success','Please login. After that click the link from get the email verification.');
            }
        }else{
            if(auth()->user()){
                if(auth()->user()->position == "admin"){
                    return redirect()->route('admin.student_list.index')->with('failed','The Email has been existed');
                }
            }
            else{
                return redirect()->route('student.create')->with('failed','The Email has been existed');
            }
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
        $student = Student::find($id);
        $user = User::find($student->user_id);
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->orderBy('programme_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        return view('admin.StudentEdit', compact('student', 'user' ,'programme', 'faculty', 'id'));
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
            'name'                 =>  'required',
            'programme_id'         =>  'string',
            'year'                 =>  'string',
            'semester'             =>  'string',
            'intake'               =>  'string',
        ]);

        $student_id  = $request->get('student_id');
        $email       = $request->get('student_id')."@sc.edu.my";
        $student     = Student::find($id);
        $user        = User::find($student->user_id);

        if($student_id != $student['student_id']){
            $checkemail = User::where('email', '=', $email)->first();
            if ($checkemail === null) {
                $user->email = $email;
                $student->student_id = $student_id;
            }else{
                return redirect()->back()->with('failed','The Email has been existed');
            }
        }
        $user->name             = $request->get('name');
        $student->programme_id  = $request->get('programme');
        $student->year          = $request->get('year');
        $student->semester      = $request->get('semester');
        $student->intake        = $request->get('intake');
        $student->save(); 
        $user->save();

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.student_list.index')->with('success','Data Updated');
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
}
