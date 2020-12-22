<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Student;
use App\Staff;
use App\Faculty;
use App\Programme;
use App\Semester;
use App\Subject;
use App\Course;
use App\Timetable;
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
        $user_id       = auth()->user()->user_id;
        $student       = Student::where('user_id', '=', $user_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('courses.status','=','Active')
                    ->where('assign_student_course.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $timetable = DB::table('assign_student_course')
                    ->join('timetable', 'timetable.course_id', '=', 'assign_student_course.course_id')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->select('timetable.*','courses.*','subjects.*','assign_student_course.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('timetable.status','=','Active')
                    ->get();
        return view('home',compact('course','timetable'));
    }


    public function adminHome()
    {
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $student = DB::table('students')
                    ->select('students.*')
                    ->get();

        $staffs = DB::table('staffs')
                    ->select('staffs.*')
                    ->get();

        $faculty = DB::table('faculty')
                    ->select('faculty.*')
                    ->get();
        return view('adminHome',compact('last_semester','student','staffs','faculty'));
    }

    public function chartStudent()
    {
        
        $result = DB::table('semesters')
                    ->join('students','students.semester','=','semesters.semester_id')
                    ->select('students.*','semesters.semester_name as name',DB::raw("COUNT(students.semester) as count"))
                    ->groupBY('students.semester')
                    ->get();
        return response()->json($result);
    }

    public function chartProgramme()
    {
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $result = DB::table('programmes')
                    ->join('students', 'programmes.programme_id','=','students.programme_id')
                    ->select('students.*','programmes.programme_name as name',DB::raw("COUNT(students.programme_id) as count"))
                    ->groupBY('students.programme_id')
                    ->where('students.semester','=',$semester_id)
                    ->get();
        return response()->json($result);
    }

    public function teacherHome()
    {   
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $timetable = DB::table('timetable')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->select('timetable.*','courses.*','subjects.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('timetable.status','=','Active')
                    ->get();

        if($last_semester->semester =='A'){
            $weeks = 7;
            $startDate = $last_semester->startDate;
        }else{
            $weeks = 14;
            $startDate = $last_semester->startDate;
        }
        $this_week = 0;
        for($i=1;$i<=$weeks;$i++){
            if($i==1){
            foreach($timetable as $row){
                $week = "Next ".$row->week;
                $NewDate = date('Y-m-d', strtotime($startDate . $week));
                $date = date('Y-m-d');
                if($NewDate==$date){
                    $this_week = $i;
                }
            }
            }else{
                $startDate = strtotime($last_semester->startDate);
                $add_date = $startDate+(($i-1)*(86400*7));
                $add_startDate = date('Y-m-d',$add_date);
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
                    $date = date('Y-m-d');
                    if($NewDate==$date){
                        $this_week = $i;
                    }
                }
            }
        }
        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->select('attendance.*','timetable.*','courses.*')
                    ->where('attendance.A_week','=',$this_week)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->get();

        return view('teacherHome',compact('course','timetable','last_semester','attendance'));
    }
    public function hodHome()
    {  
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $timetable = DB::table('timetable')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->select('timetable.*','courses.*','subjects.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('timetable.status','=','Active')
                    ->get();

        if($last_semester->semester =='A'){
            $weeks = 7;
            $startDate = $last_semester->startDate;
        }else{
            $weeks = 14;
            $startDate = $last_semester->startDate;
        }
        $this_week = 0;
        for($i=1;$i<=$weeks;$i++){
            if($i==1){
            foreach($timetable as $row){
                $week = "Next ".$row->week;
                $NewDate = date('Y-m-d', strtotime($startDate . $week));
                $date = date('Y-m-d');
                if($NewDate==$date){
                    $this_week = $i;
                }
            }
            }else{
                $startDate = strtotime($last_semester->startDate);
                $add_date = $startDate+(($i-1)*(86400*7));
                $add_startDate = date('Y-m-d',$add_date);
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
                    $date = date('Y-m-d');
                    if($NewDate==$date){
                        $this_week = $i;
                    }
                }
            }
        }
        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->select('attendance.*','timetable.*','courses.*')
                    ->where('attendance.A_week','=',$this_week)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->get();

        return view('hodHome',compact('course','timetable','last_semester','attendance'));
    }
    public function deanHome()
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $timetable = DB::table('timetable')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->select('timetable.*','courses.*','subjects.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('timetable.status','=','Active')
                    ->get();

        if($last_semester->semester =='A'){
            $weeks = 7;
            $startDate = $last_semester->startDate;
        }else{
            $weeks = 14;
            $startDate = $last_semester->startDate;
        }
        $this_week = 0;
        for($i=1;$i<=$weeks;$i++){
            if($i==1){
            foreach($timetable as $row){
                $week = "Next ".$row->week;
                $NewDate = date('Y-m-d', strtotime($startDate . $week));
                $date = date('Y-m-d');
                if($NewDate==$date){
                    $this_week = $i;
                }
            }
            }else{
                $startDate = strtotime($last_semester->startDate);
                $add_date = $startDate+(($i-1)*(86400*7));
                $add_startDate = date('Y-m-d',$add_date);
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
                    $date = date('Y-m-d');
                    if($NewDate==$date){
                        $this_week = $i;
                    }
                }
            }
        }
        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->select('attendance.*','timetable.*','courses.*')
                    ->where('attendance.A_week','=',$this_week)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->get();

        return view('deanHome',compact('course','timetable','last_semester','attendance'));
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
