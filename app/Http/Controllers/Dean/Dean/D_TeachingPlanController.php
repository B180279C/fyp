<?php

namespace App\Http\Controllers\Dean\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Staff;
use App\User;
use App\Teaching_Plan;
use App\Plan_Topic;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Imports\syllabusRead;
use App\TP_Assessment_Method;
use App\TP_CQI;
use App\Action_V_A;

class D_TeachingPlanController extends Controller
{
	public function DeanTeachingPlan($id)
	{
		    $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.verified_by', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $id)
                 ->get();

        $verified_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
        $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        // $approved_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        // $approved_person_name = User::where('user_id', '=', $approved_by->user_id)->firstOrFail();

        $approved_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'HoD')
                 ->where('staffs.department_id','=',$department_id)
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
            return view('dean.Dean.Teaching_Plan.D_TeachingPlan',compact('course','TP','topic','TP_Ass','TP_CQI','action','verified_person_name','verified_by','approved_by'));
        }else{
            return redirect()->back();
        }
	}

	public function D_TP_VerifyAction(Request $request)
	{
		$course_id = $request->get('course_id');
		$verify    = $request->get('verify');
		$remarks   = $request->get('remarks');
		$result    = $request->get('result');

		if($remarks == "<p><br></p>"){
			$remarks = "";
		}
		$action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $course_id)
                  ->where('action_type','=','TP')
                  ->where('status','=','Waiting For Approved')
                  ->where('for_who','=','HOD')
                  ->orderByDesc('action_id')
                  ->get();

        $action_save = Action_V_A::where('action_id', '=', $action[0]->action_id)->firstOrFail();

        if($result=="Approve"){
        	$action_save->status  = "Approved";
        	$action_save->for_who = "";
	    	  $action_save->remarks = $remarks;
          $action_save->approved_date = date("Y-j-n");
        }else{
        	$action_save->status  = "Rejected";
	    	  $action_save->remarks = $verify."///".$remarks;
        }
	    $action_save->save();
	    if($result=="Approve"){
	    	return redirect()->back()->with('success','The Teaching Plan has been Approved.');
	    }else{
	    	return redirect()->back()->with('success','The Teaching Plan has been Rejected.');
	    }
	}
}