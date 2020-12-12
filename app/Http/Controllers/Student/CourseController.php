<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Student;
use App\Faculty;
use App\Programme;
use App\Semester;
use App\Subject;
use App\Course;
use Excel;
use App\Imports\CoursesImport;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        return view('student.Course.CourseIndex',compact('course'));
    }

    public function searchTeachCourse(Request $request)
    {
        $user_id       = auth()->user()->user_id;
        $student       = Student::where('user_id', '=', $user_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;

        $value = $request->get('value');

        if(auth()->user()->position=="student"){
            $character = "/students";
        }

        $result = "";
        if($value!=""){
            $course = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
                    })
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('courses.status','=','Active')
                    ->where('assign_student_course.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            $result .= '<p id="marking">
                            <span style="padding:0px 10px;">Plan</span>
                            <span style="padding:0px 10px;">Note</span>
                            <span style="padding:0px 10px;">Assessment</span>
                        </p>';
            if ($course->count()) {
                foreach($course as $row){
                    $result .= '<a href="'.$character.'/course/action/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1">';
                    $result .= '<img src="'.url("image/subject.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                    $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->subject_code." ".$row->subject_name." ( ".$row->name.' ) </p>';
                    $result .= '<p id="mark_data">
                                  <i class="fa fa-check correct" aria-hidden="true"></i>
                                  <i class="fa fa-check correct" aria-hidden="true"></i>
                                  <i class="fa fa-times wrong" aria-hidden="true" style="width: 90px"></i>
                              </p>';
                    $result .= '</div></div></a>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
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
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Newest Semester of Courses</p>';
            $result .= '<p id="marking">
                            <span style="padding:0px 10px;">Plan</span>
                            <span style="padding:0px 10px;">Note</span>
                            <span style="padding:0px 10px;">Assessment</span>
                        </p>';
            $result .= '</div>';
            foreach($course as $row){
                $result .= '<a href="'.$character.'/course/action/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1">';
                $result .= '<img src="'.url("image/subject.png").'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->subject_code." ".$row->subject_name." ( ".$row->name.' ) </p>';
                $result .= '<p id="mark_data">
                                <i class="fa fa-check correct" aria-hidden="true"></i>
                                <i class="fa fa-check correct" aria-hidden="true"></i>
                                <i class="fa fa-times wrong" aria-hidden="true" style="width: 90px"></i>
                            </p>';
                $result .= '</div></div></a>';
            }
        }
        return $result;
    }


    public function courseAction($id){
        $user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();
        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$id)
                    ->get();
        if(count($course)>0){
            return view('student.Course.CourseAction',compact('course','id'));
        }else{
            return redirect()->back();
        }
    }
}
