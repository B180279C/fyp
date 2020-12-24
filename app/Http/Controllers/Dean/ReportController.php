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
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->orderByDesc('semester_name')
                    ->get();

            $action = DB::table('action_v_a')
                      ->join('courses','courses.course_id','=','action_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as status_data')
                      ->where('departments.faculty_id','=',$faculty_id)
                      ->where('courses.status','=','Active')
                      ->groupBy('action_v_a.course_id')
                      ->orderByDesc('action_v_a.action_id')
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
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->orderByDesc('semester_name')
                    ->get();

            $action = DB::table('action_v_a')
                      ->join('courses','courses.course_id','=','action_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as status_data')
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->groupBy('action_v_a.course_id')
                      ->orderByDesc('action_v_a.action_id')
                      ->get();
        }
		return view('dean.Report.TPReport',compact('course','action'));
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
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->orderByDesc('semester_name')
                    ->get();

            $action = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                  ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as status_data')
                  ->where('departments.faculty_id','=',$faculty_id)
                  ->where('courses.status','=','Active')
                  ->groupBy('actionca_v_a.course_id')
                  ->orderByDesc('actionca_v_a.actionCA_id')
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
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->orderByDesc('semester_name')
                    ->get();

            $action = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                  ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as status_data')
                  ->where('departments.faculty_id','=',$faculty_id)
                  ->where('courses.status','=','Active')
                  ->groupBy('actionca_v_a.course_id')
                  ->orderByDesc('actionca_v_a.actionCA_id')
                  ->get();
        }
    return view('dean.Report.AssessmentReport',compact('course','action'));
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
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->orderByDesc('semester_name')
                    ->get();

            $action = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                  ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionfa_v_a.status as status_data')
                  ->where('departments.faculty_id','=',$faculty_id)
                  ->where('courses.status','=','Active')
                  ->groupBy('actionfa_v_a.course_id')
                  ->orderByDesc('actionfa_v_a.actionFA_id')
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
                    ->where('courses.status','=','Active')
                    ->orderByDesc('course_id')
                    ->orderByDesc('semester_name')
                    ->get();

            $action = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                  ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionfa_v_a.status as status_data')
                  ->where('departments.department_id','=',$department_id)
                  ->where('courses.status','=','Active')
                  ->groupBy('actionfa_v_a.course_id')
                  ->orderByDesc('actionfa_v_a.actionFA_id')
                  ->get();
        }
    return view('dean.Report.FinalAssessmentReport',compact('course','action'));
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
                    ->get();        
        }
    return view('dean.E_portfolio.E_Portfolio_List',compact('course_reviewer'));
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
                    ->where('courses.semester','=',$semester_id)
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

        return view('dean.E_portfolio.E_Portfolio_View_List',compact('course','assessments','assessment_list','lecturer_result','ass_final','assessment_final','lecturer_fx_result','syllabus','action','ca_action','fa_action','lecture_note'));
    }
}
