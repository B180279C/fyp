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
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;

        $course_reviewer = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.reviewer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->get();

        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.reviewer', '!=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        if($course[0]->position=="Dean"){
        	return view('dean.Dean.D_CourseIndex',compact('course','course_reviewer'));	
        }else{
        	return redirect()->back();
        }
	}

	public function searchCourse(Request $request)
    {
        $user_id     = auth()->user()->user_id;
        $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id  = $staff_dean->faculty_id;
        $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id = $last_semester->semester_id;

        $value = $request->get('value');

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
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                    })             
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if ($course->count()) {
                foreach($course as $row){
                    $result .= '<a href="" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1">';
                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name">';
                    $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                    $result .= '</div>';
                    $result .= '<div class="col-1" id="course_action">';
                    $result .= '<i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                    $result .= '<i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                    $result .= '</div></div></a>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $course_reviewer = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.reviewer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->get();

	        $course = DB::table('courses')
	                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
	                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
	                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
	                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
	                    ->join('staffs', 'staffs.id','=','courses.lecturer')
	                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
	                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
	                    ->where('courses.semester','=',$semester_id)
	                    ->where('courses.reviewer', '!=', $staff_dean->id)
	                    ->where('courses.status','=','Active')
	                    ->orderBy('programmes.programme_id')
	                    ->get();

            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>';
            $result .= '</div>';
            foreach($course_reviewer as $row){
                $result .= '<a href="/Dean/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1">';
                $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col" id="course_name">';
                $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                $result .= '</div>';
                $result .= '<div class="col-1" id="course_action">';
                $result .= '<i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                $result .= '<i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                $result .= '</div></div></a>';
            }
            foreach($course as $row){
                $result .= '<a href="/Dean/course/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1">';
                $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col" id="course_name">';
                $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                $result .= '</div>';
                $result .= '<div class="col-1" id="course_action">';
                $result .= '<i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                $result .= '<i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                $result .= '</div></div></a>';
            }
        }
        return $result;
    }

    public function DeanAction($id)
    {
    	$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

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
            return view('dean.Dean.DeanAction',compact('course','id','student','note','tp','tp_ass','tp_cqi'));
        }else{
            return redirect()->route('dean.home');
        }
    }
}