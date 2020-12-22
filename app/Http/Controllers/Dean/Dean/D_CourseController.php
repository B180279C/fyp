<?php

namespace App\Http\Controllers\Dean\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Staff;
use App\Faculty;
use App\Programme;
use App\Semester;
use App\Subject;
use App\Course;
use App\Teaching_Plan;
use App\TP_Assessment_Method;
use App\TP_CQI;
use Excel;
use App\Imports\CoursesImport;

class D_CourseController extends Controller
{
	public function index()
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $id            = $staff_dean->id;

        if(auth()->user()->position=="Dean"){
            $character = '';
            $course_reviewer = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->where('courses.status','=','Active')
                    ->get();
            $action3 = DB::table('actionfa_v_a')
                    ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->where('courses.status','=','Active')
                    ->where('actionfa_v_a.status','=','Waiting For Approve')
                    ->where('actionfa_v_a.for_who','=','Dean')
                    ->get();
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
            $course_reviewer = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('departments.department_id','=',$department_id)
                    ->where('courses.status','=','Active')
                    ->get();

            $action = DB::table('action_v_a')
                      ->join('courses','courses.course_id','=','action_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->where('action_v_a.status','=','Waiting For Approved')
                      ->where('action_v_a.for_who','=','HOD')
                      ->get();

            $action2 = DB::table('actionca_v_a')
                      ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->where('actionca_v_a.status','=','Waiting For Verified')
                      ->where('actionca_v_a.for_who','=','HOD')
                      ->get();

            $action3 = DB::table('actionfa_v_a')
                      ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->where('actionfa_v_a.status','=','Waiting For Verified')
                      ->where('actionfa_v_a.for_who','=','HOD')
                      ->get();
        }
        if(auth()->user()->position=="Dean"){
        	return view('dean.Reviewer.D_CourseIndex',compact('course_reviewer','action3'));	
        }else{
          return view('dean.Reviewer.D_CourseIndex',compact('course_reviewer','action','action2','action3'));  
        }
	}

	public function searchCourse(Request $request)
    {
        $user_id     = auth()->user()->user_id;
        $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id  = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id = $last_semester->semester_id;

        $value = $request->get('value');

        $result = "";
        if($value!=""){
            if(auth()->user()->position=="Dean"){
                $character = '';
                $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                    })             
                    ->get();
            }else{
                $character = '/hod';
                $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.department_id', '=', $department_id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                    })             
                    ->get();
            }
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if($course->count()) {
                foreach($course as $row){
                    $result .= '<a href="'.$character.'/Reviewer/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1">';
                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name">';
                    $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                    $result .= '</div>';
                    $result .= '</div></a>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
          if(auth()->user()->position=="Dean"){
              $character = '';
              $course_reviewer = DB::table('courses')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.faculty_id','=',$faculty_id)
                      ->where('courses.status','=','Active')
                      ->get();
            }else{
              $character = '/hod';
              $course_reviewer = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('departments.department_id','=',$department_id)
                    ->where('courses.status','=','Active')
                    ->get();
            }
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>';
            $result .= '</div>';
            foreach($course_reviewer as $row){
                  $result .= '<a href="'.$character.'/Reviewer/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                  $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                  $result .= '<div class="col-1">';
                  $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                  $result .= '</div>';
                  $result .= '<div class="col" id="course_name">';
                  $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                  $result .= '</div>';
                  $result .= '</div></a>';
            }
        }
        return $result;
    }

    public function DeanAction($id)
    {
    	  $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        if(auth()->user()->position=="Dean"){

          $course = DB::table('courses')
                   ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                   ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                   ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                   ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                   ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                   ->join('staffs', 'staffs.id','=','courses.lecturer')
                   ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                   ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                   ->where('courses.course_id', '=', $id)
                   ->where('faculty.faculty_id','=',$faculty_id)
                   ->get();

        }else if(auth()->user()->position=="HoD"){

          $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $id)
                 ->where('departments.department_id','=',$department_id)
                 ->get();

        }
       	$student = DB::table('assign_student_course')
                 ->select('assign_student_course.*')
                 ->where('course_id', '=', $id)
                 ->where('status','=','Active')
                 ->get();

    		$note = DB::table('lecture_notes')
                     ->select('lecture_notes.*')
                     ->where('course_id', '=', $id)
                     ->where('status','=','Active')
                     ->get();

        $tp = DB::table('teaching_plan')
                 ->select('teaching_plan.*')
                 ->where('course_id', '=', $id)
                 ->get();

        $tp_ass = DB::table('tp_assessment_method')
                 ->select('tp_assessment_method.*')
                 ->where('course_id', '=', $id)
                 ->get();

        $tp_cqi = DB::table('tp_cqi')
                 ->select('tp_cqi.*')
                 ->where('course_id', '=', $id)
                 ->where('status','=','Active')
                 ->get();

        if(count($course)>0){
            return view('dean.Reviewer.DeanAction',compact('course','id','student','note','tp','tp_ass','tp_cqi'));
        }else{
            return redirect()->route('home');
        }
    }
}