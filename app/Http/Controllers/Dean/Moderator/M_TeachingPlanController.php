<?php

namespace App\Http\Controllers\Dean\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Staff;
use App\Teaching_Plan;
use App\Plan_Topic;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Imports\syllabusRead;
use App\TP_Assessment_Method;
use App\TP_CQI;
use App\Action_V_A;

class M_TeachingPlanController extends Controller
{
	public function ModeratorTeachingPlan($id)
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

        $TP = DB::table('teaching_plan')
        	->select('teaching_plan.*')
        	->where('teaching_plan.course_id','=',$id)
        	->get();

       	$topic = DB::table('plan_topics')
       			->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
       			->select('plan_topics.*','teaching_plan.*')
       			->where('teaching_plan.course_id','=',$id)
       			->get();
       			
        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $TP_CQI = DB::table('tp_cqi')
                  ->select('tp_cqi.*')
                  ->where('course_id', '=', $id)
                  ->where('tp_cqi.status','=','Active')
                  ->get();

        $action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('action_id')
                  ->get();

        if(count($course)>0){
            return view('dean.Moderator.Teaching_Plan.M_TeachingPlan',compact('course','TP','topic','TP_Ass','TP_CQI','action'));
        }else{
            return redirect()->back();
        }
	}

	public function M_TP_VerifyAction(Request $request)
	{
		$course_id = $request->get('course_id');
		$verify = $request->get('verify');
		$remarks = $request->get('remarks');
		$result = $request->get('result');

		if($remarks == "<p><br></p>"){
			$remarks = "";
		}
		$action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $course_id)
                  ->where('action_type','=','TP')
                  ->where('status','=','Waiting For Verified')
                  ->where('for_who','=','Moderator')
                  ->orderByDesc('action_id')
                  ->get();

        $action_save = Action_V_A::where('action_id', '=', $action[0]->action_id)->firstOrFail();

        if($result=="Verify"){
        	$action_save->status  = "Waiting For Approved";
        	$action_save->for_who = "HOD";
	    	$action_save->remarks = $remarks;
        }else{
        	$action_save->status  = "Rejected";
	    	$action_save->remarks = $verify."///".$remarks;
        }
	    $action_save->save();
	    if($result=="Verify"){
	    	return redirect()->back()->with('success','Teaching Plan Have been submitted to HOD.');
	    }else{
	    	return redirect()->back()->with('success','The Teaching Plan has been rejected.');
	    }
	}

	
}