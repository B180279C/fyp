<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Student;
use App\User;
use App\Programme;
use App\Faculty;
use App\Semester;
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
                    ->join('semesters','students.semester', '=', 'semesters.semester_id')
                    ->select('students.*', 'users.email', 'users.name', 'programmes.programme_name','programmes.short_form_name','semesters.*')
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
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        return view('student.StudentCreate', compact('programme','faculty','semester'));
    }

    public function AdminCreateStudent()
    {
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->orderBy('programme_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        return view('admin.StudentCreate', compact('programme','faculty','semester'));
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

        $email = $request->get('student_id')."@sc.edu.my"; 
        $checkemail = User::where('email', '=', $email)->first();
        $image_name = "";

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

            if($request->get('student_image')!=""){
                $image = Storage::disk('private')->get("fake/student_Image/".$request->get('student_image'));
                Storage::disk('private')->put('studentImage/'.$request->get('student_image'), $image); 
                Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
            }

            $student = new Student([
                'user_id'           => $user_id,
                'student_id'        => $request->get('student_id'),
                'programme_id'      => $request->get('programme'),
                'semester'          => $request->get('semester'),
                'intake'            => $request->get('intake'),
                'student_image'     => $request->get('student_image'),
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
                    if($request->get('student_image')!=""){
                        Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
                    }
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
    public function show($image_name)
    {
        $storagePath = storage_path('/private/studentImage/' . $image_name);
        return Image::make($storagePath)->response();
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
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        return view('admin.StudentEdit', compact('student', 'user' ,'programme', 'faculty','semester', 'id'));
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
                if($request->get('student_image')!=""){
                    Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
                }
                return redirect()->back()->with('failed','The Email has been existed');
            }
        }

        $user->name             = $request->get('name');
        $student->programme_id  = $request->get('programme');
        $student->semester      = $request->get('semester');
        $student->intake        = $request->get('intake');

        if($request->get('student_image')!=""){
            $student->student_image  = $request->get('student_image');
            $image = Storage::disk('private')->get("fake/student_Image/".$request->get('student_image'));
            Storage::disk('private')->put('studentImage/'.$request->get('student_image'), $image); 
            Storage::disk('private')->delete('fake/student_Image/'.$request->get('student_image'));
        }

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

    public function removeImage(Request $request)
    {
        $value = $request->get('value');
        $image = $request->get('image');

        $student = Student::where('id', '=', $value)->firstOrFail();
        $student->student_image = "";
        $student->save();

        Storage::disk('private')->delete('/studentImage/'.$image);
        return $image;
    }
}
