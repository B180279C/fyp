<?php

namespace App\Http\Controllers\Dean\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Staff;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Imports\syllabusRead;
use App\TP_Assessment_Method;

class M_AssessmentController extends Controller
{
	public function ModeratorAssessment($id)
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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $action = DB::table('actionCA_v_a')
                  ->select('actionCA_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('actionCA_id')
                  ->get();

        if(count($course)>0){
            return view('dean.Moderator.Assessment.M_AssessmentList',compact('course','assessments','TP_Ass','action'));
        }else{
            return redirect()->back();
        }
	}
}