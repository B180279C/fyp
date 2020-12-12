<?php

namespace App\Http\Controllers\Dean\Course;

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

class E_PortfolioController extends Controller
{
	public function viewE_Portfolio($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;

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

        return view('dean.CoursePortFolio.E_portfolio.viewE_Portfolio',compact('course','assessments','assessment_list','lecturer_result','ass_final','assessment_final','lecturer_fx_result','syllabus','action','ca_action','fa_action','lecture_note'));
	}
}

