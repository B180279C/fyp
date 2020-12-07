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

class C_StudentListController extends Controller
{
	public function StudentList($id)
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

        $batch = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course[0]->course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->groupBy('students.batch')
                    ->get();

        $assign_student = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course[0]->course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
        if(count($course)>0){
            return view('dean.CoursePortfolio.Student_List.C_StudentList',compact('course','id','batch','assign_student'));
        }else{
            return redirect()->back();
        }
	}
}