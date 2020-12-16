<?php

namespace App\Http\Controllers\Dean\Course;

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
use App\Timetable;
use App\Attendance;

class C_AttendanceController extends Controller
{
    public function viewAttendance($id)
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

        $count_student = count($assign_student);
        $total = ($timetable_count*$count_student);
        $average = 100;
        $completed = 0;
        if($absent_person!=0){
            $average = ($absent_person/$total)*100; 
        }

        if($timetable_count!=0){
            $completed = ($total_tt/$timetable_count)*100;
        }
        if(count($course)>0){
            return view('dean.CoursePortFolio.Attendance.viewAttendance',compact('course','timetable','attendance','id','average','completed','count_student'));
        }else{
            return redirect()->back();
        }
    }

    public function viewStudentList($id,$date)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $t_id          = explode('-',$id);
        $tt_id         = $t_id[0];
        $week          = $t_id[1];
        $fill_up       = $t_id[2];
        $timetable     = Timetable::where('tt_id', '=', $id)->firstOrFail();
        $course_id     = $timetable->course_id;

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
                     ->where('courses.course_id', '=', $course_id)
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
                     ->where('courses.course_id', '=', $course_id)
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        $assign_student = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->orderBy('students.batch')
                    ->get();

        $attendance = DB::table('attendance')
                    ->select('attendance.*')
                    ->where('attendance.tt_id','=',$tt_id)
                    ->where('attendance.A_date','=',$date)
                    ->where('attendance.less_hour','=',$fill_up)
                    ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.Attendance.viewStudentList',compact('course','tt_id','assign_student','date','timetable','attendance','week','fill_up'));
        }else{
            return redirect()->back();
        }
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
?>
