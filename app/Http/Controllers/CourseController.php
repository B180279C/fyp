<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Staff;
use App\Faculty;
use App\Programme;
use App\Subject;
use App\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_id      = auth()->user()->user_id;
        $staff_dean   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id   = $staff_dean->faculty_id;
        $faculty_name = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->where('faculty.faculty_id', '=', $faculty_id)
                    ->orderBy('programme_name')
                    ->get();
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->get();
        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '!=', $faculty_id)
                    ->get();
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        return view('dean.CourseCreate', compact('programme', 'staffs','lct','faculty','faculty_name','semester'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $staff = Staff::where('user_id', '=', Auth::User()->user_id)->firstOrFail();
            if($staff->faculty_id=="8"){
                $course_type = "MPU";
            }else{
                $course_type = "Normal";
            }
        $checkexists = DB::table('courses')
                    ->select('courses.*')
                    ->where('courses.subject_id','=',$request->get('subject'))
                    ->where('courses.semester','=',$request->get('semester'))
                    ->where('courses.status','=',"Active")
                    ->get();

        if (count($checkexists) === 0) {
            $course = new Course([
                'subject_id'        => $request->get('subject'),
                'course_type'       => $course_type,
                'semester'          => $request->get('semester'),
                'lecturer'          => $request->get('lecturer'),
                'status'            => "Active",
            ]);
            $course->save();
            return redirect()->route('dean.C_potrfolio.index')->with('success','Data Added');
        }else{
            return redirect()->back()->with('failed','The Subject has been existed');
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
        $user_id      = auth()->user()->user_id;
        $staff_dean   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id   = $staff_dean->faculty_id;
        $faculty_name = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->where('faculty.faculty_id', '=', $faculty_id)
                    ->orderBy('programme_name')
                    ->get();
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->get();
        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '!=', $faculty_id)
                    ->get();
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        $course  = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->select('courses.*', 'subjects.*')
                    ->where('courses.course_id', '=', $id)
                    ->get();
        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $course[0]->programme_id)
                    ->get();
        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $course[0]->programme_id)
                    ->groupBy('subject_type')
                    ->get();
        $staff_check_inFaculty = DB::table('staffs')
                    ->select('staffs.*')
                    ->where('staffs.id', '=', $course[0]->lecturer)
                    ->where('staffs.faculty_id','=', $faculty_id)
                    ->get();
        $count_staff_InFaculty = count($staff_check_inFaculty);
        return view('dean.CourseEdit', compact('programme', 'staffs','lct','faculty','faculty_name','semester','group','subjects','course','count_staff_InFaculty', 'id'));
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
        $course                = Course::where('course_id', '=', $id)->firstOrFail();

        if($course->subject_id!=$request->get('subject')){
            $checkexists = DB::table('courses')
                        ->select('courses.*')
                        ->where('courses.subject_id','=',$request->get('subject'))
                        ->where('courses.semester','=',$request->get('semester'))
                        ->where('courses.status','=',"Active")
                        ->get();
            if (count($checkexists) === 0) {
                $course->subject_id    = $request->get('subject');
            }else{
                return redirect()->back()->with('failed','The Subject has been existed');
            }
        }
        $course->semester      = $request->get('semester');
        $course->lecturer      = $request->get('lecturer');
        $course->save();
        return redirect()->route('dean.C_potrfolio.index')->with('success','Data Updated');
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


    public function courseSubject(Request $request)
    {
        $value = $request->get('value');

        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $value)
                    ->get();
        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $value)
                    ->groupBy('subject_type')
                    ->get();

        $data = "";
        $result = "";
        foreach($group as $row_group){
            $data .= "<optgroup label='$row_group->subject_type'>";
            foreach($subjects as $row){
                if($row_group->subject_type == $row->subject_type){
                    $subject_id   = $row->subject_id;
                    $subject_code = $row->subject_code;
                    $subject_name = $row->subject_name;
                    $data .= "<option value=$subject_id class='option-group'>$subject_code : $subject_name</option>";
                }
            }
            $data .= "</optgroup>";
        }

        if($data==""){
            return "null";
        }

        $result = $data;

        return $result;
    }

    public function removeActiveCourse($id){
        $course = Course::where('course_id', '=', $id)->firstOrFail();
        $course->status  = "Remove";
        $course->save();
        return redirect()->back()->with('success','Remove Successfully');
    }
}
