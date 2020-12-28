<?php

namespace App\Http\Controllers\Dean\Moderator;

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

class M_CourseController extends Controller
{
	public function index()
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
                    ->where('courses.moderator', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                  ->where('courses.semester','=',$semester_id)
                  ->where('courses.moderator', '=', $staff_dean->id)
                  ->where('courses.status','=','Active')
                  ->where('action_v_a.status','=','Waiting For Verified')
                  ->where('action_v_a.for_who','=','Moderator')
                  ->get();

        $action2 = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                  ->where('courses.semester','=',$semester_id)
                  ->where('courses.moderator', '=', $staff_dean->id)
                  ->where('courses.status','=','Active')
                  ->where('actionca_v_a.status','=','Waiting For Moderation')
                  ->where('actionca_v_a.for_who','=','Moderator')
                  ->get();

        $action3 = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                  ->where('courses.semester','=',$semester_id)
                  ->where('courses.moderator', '=', $staff_dean->id)
                  ->where('courses.status','=','Active')
                  ->where('actionfa_v_a.status','=','Waiting For Moderation')
                  ->where('actionfa_v_a.for_who','=','Moderator')
                  ->get();

        return view('dean.Moderator.M_CourseIndex',compact('course','action','action2','action3'));
	}


  public static function getAction($course_id)
  {
        $course_count  = 0;
        $course_tp_action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as action_status')
                  ->where('courses.course_id','=',$course_id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('action_v_a.action_id')
                  ->first();
        if($course_tp_action!=""){
            if($course_tp_action->action_status=="Waiting For Verified"){
                $course_count++;
            }
        }
        $course_CA_action = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as action_status')
                  ->where('courses.course_id','=',$course_id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('actionca_v_a.actionCA_id')
                  ->first();
        if($course_CA_action!=""){
            if($course_CA_action->action_status=="Waiting For Moderation"){
                $course_count++;
            }
        }
        $course_FA_action = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionfa_v_a.status as action_status')
                  ->where('courses.course_id','=',$course_id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('actionfa_v_a.actionFA_id')
                  ->first();
        if($course_FA_action!=""){
            if($course_FA_action->action_status=="Waiting For Moderation"){
                $course_count++;
            }
        }
        return $course_count;
  }

	public function searchModeratorCourse(Request $request)
    {
        $user_id     = auth()->user()->user_id;
        $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id  = $staff_dean->faculty_id;
        $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id = $last_semester->semester_id;

        $value = $request->get('value');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }

        $result = "";
        if($value!=""){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.moderator', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                    })
                    ->orderByDesc('semesters.semester_name')
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if ($course->count()) {
                foreach($course as $row){
                    $count = $this->getAction($row->course_id);
                    $result .= '<a href="'.$character.'/Moderator/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:13px 10px;color:#0d2f81;">';
                    $result .= '<div class="col-1 align-self-center">';
                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                    $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.' ) </p>';
                    if($count>0){
                      $result .= '<span class="notification_num">';
                      $result .= '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                      $result .= '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                      $result .= '</span>';
                    }
                    $result .= '</div></div></a>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.moderator', '=', $staff_dean->id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Newest Semester of Courses</p>';
            $result .= '</div>';
            foreach($course as $row){
                $count = $this->getAction($row->course_id);
                $result .= '<a href="'.$character.'/Moderator/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:13px 10px;color:#0d2f81;">';
                    $result .= '<div class="col-1 align-self-center">';
                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                    $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.' ) </p>';
                    if($count>0){
                      $result .= '<span class="notification_num">';
                      $result .= '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                      $result .= '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                      $result .= '</span>';
                    }
                    $result .= '</div></div></a>';
            }
        }
        return $result;
    }

	public function ModeratorAction($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $id)
                 ->get();

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
            return view('dean.Moderator.ModeratorAction',compact('course','id','student','note','tp','tp_ass','tp_cqi'));
        }else{
            return redirect()->back();
        }
	}
}