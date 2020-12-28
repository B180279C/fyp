<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\User;
use App\Staff;
use App\Department;
use App\Programme;
use App\Faculty;
use App\Subject;
use ZipArchive;
use File;

class ReportController extends Controller
{
	public function ReportAction()
	{
		return view('dean.Report.ReportAction');
	}


	public function TPReport()
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
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->get();
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.department_id','=',$department_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->get();
        }
		return view('dean.Report.TPReport',compact('course'));
	}

  public static function getTPaction($course_id)
  {
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

    $status = "Pending";
    if($course_tp_action!=""){
      $status = $course_tp_action->action_status;
    }
    return $status;
  }

  public function AssessmentReport()
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
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->get();

        }else if(auth()->user()->position=="HoD"){

            $character = '/hod';
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.department_id','=',$department_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->get();
        }
    return view('dean.Report.AssessmentReport',compact('course'));
  }

  public static function getCAaction($course_id)
  {
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

    $status = "Pending";
    if($course_CA_action!=""){
      $status = $course_CA_action->action_status;
    }
    return $status;
  }

  public function FinalAssessmentReport()
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
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->get();

        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.department_id','=',$department_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->get();
        }
    return view('dean.Report.FinalAssessmentReport',compact('course'));
  }

  public static function getFAaction($course_id)
  {
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

    $status = "Pending";
    if($course_FA_action!=""){
      $status = $course_FA_action->action_status;
    }
    return $status;
  }

  public function E_Portfolio_List()
  {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;

        if(auth()->user()->position=="Dean"){
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
                    ->orderByDesc('courses.semester')
                    ->orderByDesc('course_id')
                    ->get();

        }else if(auth()->user()->position=="HoD"){
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
                    ->orderByDesc('courses.semester')
                    ->orderByDesc('course_id')
                    ->get();        
        }
    return view('dean.Report.E_Portfolio_List',compact('course_reviewer'));
  }

  

  public function viewListE_Portfolio($id)
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
                    ->where('course_id', '=', $id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $syllabus = $course[0]->syllabus;

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $assessment_list = DB::table('assessment_list')
                    ->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->select('assessment_list.*','assessments.*')
                    ->where('assessment_list.status', '=', 'Active')
                    ->where('assessments.course_id', '=', $id)
                    ->get();

        $lecturer_result = DB::table('assessment_result_students')
                 ->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
                 ->select('assessment_result_students.*','assessments.*')
                 ->where('assessments.course_id', '=', $id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->groupBy('assessments.ass_id')
                 ->get();

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

        $assessment_final = DB::table('assessment_final')
                    ->join('ass_final','ass_final.fx_id','=','assessment_final.fx_id')
                    ->select('assessment_final.*','ass_final.*')
                    ->where('ass_final.course_id', '=', $id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->get();

        $lecturer_fx_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        $action = DB::table('action_v_a')
                    ->select('action_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $ca_action = DB::table('actionca_v_a')
                    ->select('actionca_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Verified')
                    ->get();

        $fa_action = DB::table('actionfa_v_a')
                    ->select('actionfa_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->get();

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        $attendance = $this->viewAttendance($id);

        return view('dean.Report.E_Portfolio_View_List',compact('course','assessments','assessment_list','lecturer_result','ass_final','timetable','attendance','assessment_final','lecturer_fx_result','syllabus','action','ca_action','fa_action','lecture_note'));
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
