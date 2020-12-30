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

  public static function getAction($course_id)
  {
      $course_count  = 0;
      if(auth()->user()->position=="Dean"){
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
          if($course_FA_action->action_status=="Waiting For Approve"){
            $course_count++;
          }
        }
      }else if(auth()->user()->position=="HoD"){
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
            if($course_tp_action->action_status=="Waiting For Approved"){
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
            if($course_CA_action->action_status=="Waiting For Verified"){
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
          if($course_FA_action->action_status=="Waiting For Verified"){
            $course_count++;
          }
        }
      }
      return $course_count;
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
                    $count = $this->getAction($row->course_id);
                    $result .= '<a href="'.$character.'/Reviewer/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:13px 10px;color:#0d2f81;">';
                    $result .= '<div class="col-1 align-self-center">';
                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                    $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                    if($count>0){
                      $result .= '<span class="notification_num" >';
                      $result .= '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                      $result .= '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                      $result .= '</span>';
                    }
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
                  $count = $this->getAction($row->course_id);
                  $result .= '<a href="'.$character.'/Reviewer/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                  $result .= '<div class="col-md-12 row" style="padding:13px 10px;color:#0d2f81;">';
                  $result .= '<div class="col-1 align-self-center">';
                  $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                  $result .= '</div>';
                  $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                  $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                  if($count>0){
                    $result .= '<span class="notification_num" >';
                    $result .= '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                    $result .= '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                    $result .= '</span>';
                  }
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

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        $attendance = $this->viewAttendance($id);

        $status_TP = "Pending";
        $status_CA = "Pending";
        $status_FA = "Pending";
        $course_tp_action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as action_status')
                  ->where('courses.course_id','=',$id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('action_v_a.action_id')
                  ->first();

        if($course_tp_action!=""){
          $status_TP = $course_tp_action->action_status;
        }

        $course_CA_action = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as action_status')
                  ->where('courses.course_id','=',$id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('actionca_v_a.actionCA_id')
                  ->first();
        if($course_CA_action!=""){
            $status_CA = $course_CA_action->action_status;
        }

        $course_FA_action = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionfa_v_a.status as action_status')
                  ->where('courses.course_id','=',$id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('actionfa_v_a.actionFA_id')
                  ->first();
        if($course_FA_action!=""){
            $status_FA = $course_FA_action->action_status;
        }
        
        if(count($course)>0){
            return view('dean.Reviewer.DeanAction',compact('course','id','student','note','status_TP','status_CA','status_FA','timetable','attendance'));
        }else{
            return redirect()->route('home');
        }
    }

    public function viewAttendance($id)
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
                 ->where('courses.course_id', '=', $id)
                 ->get();

        $assign_student = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$id)
                    ->where('assign_student_course.status','=',"Active")
                    ->orderBy('students.batch')
                    ->get();

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->select('attendance.*','timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->orderBy('attendance.A_date')
                    ->orderBy('timetable.class_hour')
                    ->get();

        if($course[0]->semester =='A'){
            $weeks = 7;
            $startDate = $course[0]->startDate;
        }else{
            $weeks = 14;
            $startDate = $course[0]->startDate;
        }
        $absent_person = 0;
        $timetable_count = 0;
        $total_tt = 0;
        $take_hour = 0;
        for($i=1;$i<=$weeks;$i++){
            $count_hour = 0;
            if($i==1){
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($startDate . $week));
                    $hour = explode(',',$row->class_hour);
                    $count_hour = $count_hour + count($hour);
                    $take_hour = 0;
                    foreach($attendance as $att_row){
                        if($att_row->A_week==$i){
                            $s_hour = explode('-',$att_row->hour);
                            $e_hour = explode('-',$att_row->hour);
                            $last_hour = $this->getFullTime($s_hour[0],$e_hour[1]);
                            $timetable_hour = $att_row->class_hour;
                            $explode_th = explode(',',$timetable_hour);
                            $sperate = explode(',',$last_hour);
                            $less_hour = $att_row->less_hour;
                            if(count($sperate)>count($explode_th)){
                                $less_hour = count($explode_th)-count($sperate);
                            }
                                if($less_hour==0){
                                    for($s=0;$s<=count($sperate)-1;$s++){
                                        $take_hour++;
                                    }
                                }else if($less_hour<0){
                                    for($s=0;$s<=count($explode_th)-1;$s++){
                                        $take_hour++;
                                    }
                                }else{
                                    $take_hour= $take_hour + $less_hour;
                                }
                        }
                    }
                }
                $total_tt        = $total_tt+$take_hour;
                $timetable_count = $timetable_count + $count_hour;
            }else{
                $startDate = strtotime($course[0]->startDate);
                $add_date = $startDate+(($i-1)*(86400*7));
                $add_startDate = date('Y-m-d',$add_date);
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
                    $hour = explode(',',$row->class_hour);
                    $take_hour = 0;
                    if($row->F_or_H=="Full"){
                        $count_hour = $count_hour + count($hour);
                    }else{
                        if ($i % 2) {
                            $count_hour = $count_hour + count($hour);
                        }
                    }
                    foreach($attendance as $att_row){
                        if($att_row->A_week==$i){
                            $s_hour = explode('-',$att_row->hour);
                            $e_hour = explode('-',$att_row->hour);
                            $last_hour = $this->getFullTime($s_hour[0],$e_hour[1]);
                            $timetable_hour = $att_row->class_hour;
                            $explode_th = explode(',',$timetable_hour);
                            $sperate = explode(',',$last_hour);
                            $less_hour = $att_row->less_hour;
                            if(count($sperate)>count($explode_th)){
                                $less_hour = count($explode_th)-count($sperate);
                            }
                                if($less_hour==0){
                                    for($s=0;$s<=count($sperate)-1;$s++){
                                        $take_hour++;
                                    }
                                }else if($less_hour<0){
                                    for($s=0;$s<=count($explode_th)-1;$s++){
                                        $take_hour++;
                                    }
                                }else{
                                    $take_hour= $take_hour + $less_hour;
                                }
                        }
                    }
                }
                $total_tt        = $total_tt + $take_hour;
                $timetable_count = $timetable_count + $count_hour;    
            }
        }
        $completed = 0;

        if($timetable_count!=0){
            $completed = ($total_tt/$timetable_count)*100;
        }
        
        return $completed;
    }

    public function getFullTime($s_hour,$e_hour){
        $s_hour = intval($s_hour);
        $e_hour = intval($e_hour);
        $hour = $e_hour - $s_hour;
        $zero = "0";
        if($s_hour>=1000){
            $zero = "";
        }
        $f_time = "";
        $current = "";
        for($time = 100;$time<=$hour;$time=$time+100){
            if($current==""){
                $current = intval($s_hour+100);
                if($current>=1000){
                    $f_time .= $zero.$s_hour."-".($s_hour+100);
                }else{
                    $f_time .= $zero.$s_hour."-0".($s_hour+100);
                }
            }else{
                $zero = "0";
                if($current>=1000){
                    $zero = "";
                }
                $added_hour = intval($current+100);
                if($added_hour>=1000){
                    $f_time .= ",".$zero.$current."-".$added_hour;
                }else{
                    $f_time .= ",".$zero.$current."-0".$added_hour;
                }
                $current = $added_hour;
            }
        }
        return $f_time;
    }
}