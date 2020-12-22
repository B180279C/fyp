<?php

namespace App\Http\Controllers\Dean;

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
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
	public function viewAttendance($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','users.*','staffs.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->orderBy('timetable.class_hour')
                    ->get();

        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->select('attendance.*','timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->orderBy('attendance.A_date')
                    ->orderBy('timetable.class_hour')
                    ->get();

        if(count($course)>0){
            return view('dean.Attendance.viewAttendance',compact('course','timetable','attendance','id'));
        }else{
            return redirect()->back();
        }
	}

    public function viewStudentList($id,$date)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $t_id          = explode('-',$id);
        $tt_id         = $t_id[0];
        $week          = $t_id[1];
        $fill_up       = $t_id[2];
        $timetable     = Timetable::where('tt_id', '=', $tt_id)->firstOrFail();
        $course_id     = $timetable->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','users.*','staffs.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

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
            return view('dean.Attendance.viewStudentList',compact('course','tt_id','assign_student','date','timetable','attendance','week','fill_up'));
        }else{
            return redirect()->back();
        }
    }

    public function storeAttendance(Request $request)
    {
        $tt_id = $request->get('tt_id');
        $date = $request->get('date');
        $week = $request->get('week');
        $weekly = $request->get('weekly');
        $fill_up = $request->get('fill_up');
        $hour = $request->get('hour');
        $student_list = $request->get('student_list');
        $students = explode('/',$student_list);
        $students_status = "";
        for($i = 0;$i<(count($students)-1) ;$i++){
            $radio = $request->get('attendance'.$students[$i]);
            $students_status .= $students[$i]."=".$radio."/";
        }
        $checkAttendance = DB::table('attendance')
                    ->select('attendance.*')
                    ->where('attendance.tt_id','=',$tt_id)
                    ->where('attendance.A_date','=',$date)
                    ->where('attendance.less_hour','=',$fill_up)
                    ->get();
        if(count($checkAttendance) === 0){
            $attendance = new Attendance([
                'tt_id'           => $tt_id,
                'A_week'          => $week,
                'weekly'          => $weekly,
                'hour'            => $hour,
                'less_hour'       => $fill_up,    
                'A_date'          => $date,
                'students_status' => $students_status,
            ]);
            $attendance->save();
            return redirect()->back()->with('success','Attendance Inserted Successfully');
        }else{
            return redirect()->back()->with('failed','The Attendance are already Inserted');
        }
    }

    public function editAttendance(Request $request)
    {
        $attendance_id = $request->get('attendance_id');
        $student_list = $request->get('student_list');
        $students = explode('/',$student_list);
        $students_status = "";
        for($i = 0;$i<(count($students)-1) ;$i++){
            $radio = $request->get('attendance'.$students[$i]);
            $students_status .= $students[$i]."=".$radio."/";
        }
        $attendance    = Attendance::where('attendance_id', '=', $attendance_id)->firstOrFail();
        $attendance->students_status = $students_status;
        $attendance->save();
        return redirect()->back()->with('success','Attendance Edited Successfully');
    }

    public function openQR_Code(Request $request)
    {
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $tt_id         = $request->get('tt_id');
        $week          = $request->get('week');
        $less_hour     = $request->get('less_hour');
        $date          = $request->get('date');

        $timetable     = Timetable::where('tt_id', '=', $tt_id)->firstOrFail();
        $course_id     = $timetable->course_id;

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
                    ->where('attendance.less_hour','=',$less_hour)
                    ->get();

        if(count($attendance) === 0){
            $weekly = $timetable->week;
            $class_hour = explode(',',$timetable->class_hour);
            $s_hour = explode('-',$class_hour[0]);
            $e_hour = explode('-',$class_hour[count($class_hour)-1]);
            $hour = $s_hour[0]."-".$e_hour[1];
            $students_status = "";
            foreach($assign_student as $row){
                $students_status .= $row->student_id."=Abs/";
            }
            $selectedTime = date('H:i:s');
            $endTime = strtotime("+15 minutes", strtotime($selectedTime));
            $time_verfication = date('Y-m-d')." ".date('H:i:s', $endTime);
            $attendance = new Attendance([
                'tt_id'            => $tt_id,
                'A_week'           => $week,
                'weekly'           => $weekly,
                'hour'             => $hour,
                'less_hour'        => $less_hour,    
                'A_date'           => $date,
                'students_status'  => $students_status,
                'code'             => rand(000000,999999),
                'code_active_time' => $time_verfication,
            ]);
            $attendance->save();
            $attendance_id = $attendance->attendance_id;
            $code          = $attendance->code;
            return $attendance_id."-".$code;
        }else{
            if($attendance[0]->code==NULL){
                $selectedTime = date('H:i:s');
                $endTime = strtotime("+15 minutes", strtotime($selectedTime));
                $time_verfication = date('Y-m-d')." ".date('H:i:s', $endTime);
                $update_attendance    = Attendance::where('attendance_id', '=', $attendance[0]->attendance_id)->firstOrFail();
                $update_attendance->code = rand(000000,999999);
                $update_attendance->code_active_time = $time_verfication;
                $update_attendance->save();
                $a_id = $update_attendance->attendance_id;
                $code = $update_attendance->code;
            }else{
                $a_id = $attendance[0]->attendance_id;
                $code = $attendance[0]->code;
            }
            return $a_id."-".$code;
        }
    }

    public function QR_Code($attendance_id,$code)
    {
        return view('dean.Attendance.qr_code',compact('attendance_id','code'));
    }

    public function student_login($attendance_id,$code)
    {
        return view('dean.Attendance.StudentLogin',compact('attendance_id','code'));
    }

    public function taken_attendance(Request $request)
    {
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $attendance_id = $request->get('attendance_id');
        $code = $request->get('code');
        $email = $request->get('email');
        $password = $request->get('password');

        $attendance = DB::table('attendance')
                    ->select('attendance.*')
                    ->where('attendance.attendance_id','=',$attendance_id)
                    ->where('attendance.code','=',$code)
                    ->get();

        if(count($attendance)===0){
            return redirect()->back()->with('error',"The attendance detail got error. Please scan again the QR code.");
        }else{
            $code_active_time = date('Y-m-d H:i:s',strtotime($attendance[0]->code_active_time));
            $num_time = strtotime($code_active_time);  
            $selectedTime = strtotime(date('Y-m-d H:i:s'));
            if($selectedTime>$num_time){
                return view('dean.Attendance.StudentDie');
            }else{
                $student_id = explode('@',$email);
                if(auth()->attempt(array('email' => $email, 'password' => $password)))
                {
                    $students_status = $attendance[0]->students_status;
                    $pos = strpos($students_status, $student_id[0], 0);
                    $status = substr($students_status, ($pos+9),3);
                    if($status=="Abs"){
                        $students_status = substr_replace($students_status,'Pre',($pos+9),3);
                        $update_attendance    = Attendance::where('attendance_id', '=', $attendance[0]->attendance_id)->firstOrFail();
                        $update_attendance->students_status = $students_status;
                        $update_attendance->save();
                    }
                    return view('dean.Attendance.StudentSuccess',compact('student_id'));
                }else{
                    return redirect()->back()->with('error',"Email-Address & Password Are Wrong.");
                }    
            }
        }
    }

    public function downloadExcel($id)
    {
        return Excel::download(new AttendanceExport($id), 'Attendance.xlsx');
    }
}