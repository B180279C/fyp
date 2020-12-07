<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
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

class TeachingPlanController extends Controller
{
   	public function viewTeachingPlan($id){
   		$user_id       = auth()->user()->user_id;
      $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
      $faculty_id    = $staff_dean->faculty_id;
      $department_id = $staff_dean->department_id;
      
      $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

      $verified_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
      $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

      $approved_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'HoD')
                 ->where('staffs.department_id','=',$department_id)
                 ->get();

      // $approved_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
      // $approved_person_name = User::where('user_id', '=', $approved_by->user_id)->firstOrFail();

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
                  ->where('status','=','Active')
                  ->get();

        $action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('action_id')
                  ->get();

        if(count($course)>0){
            return view('dean.TeachingPlan.viewTeachingPlan',compact('course','TP','topic','TP_Ass','TP_CQI','action','verified_person_name','verified_by','approved_by'));
        }else{
            return redirect()->back();
        }
   	}

   	public function createTeachingPlan($id){
   		  $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
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
        if(count($course)>0){
            return view('dean.TeachingPlan.TeachingPlanCreate',compact('course','TP','topic'));
        }else{
            return redirect()->back();
        }
   	}

    public function createPreviousTP($id){
      $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $id)
                 ->get();

      if($course[0]->semester =='A'){
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
                 ->where('courses.course_id', '!=', $id)
                 ->where('courses.status', '=', 'Active')
                 ->where('semesters.semester','=','A')
                 ->orderByDesc('semesters.semester_name')
                 ->get();
        $failed = "The course have not yet open in short semester. Please write down the TP Assessment Method for this course.";
      }else{
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
                 ->where('courses.course_id', '!=', $id)
                 ->where('courses.status', '=', 'Active')
                 ->where('semesters.semester','!=',"A")
                 ->orderByDesc('semesters.semester_name')
                 ->get();
        $failed = "The course have not yet open in long semester. Please write down the TP Assessment Method for this course.";
      }
      if(count($previous)>0){
        $TP = DB::table('teaching_plan')
          ->select('teaching_plan.*')
          ->where('teaching_plan.course_id','=',$previous[0]->course_id)
          ->get();

        if(count($TP)>0){
          $delete_topic = DB::table('plan_topics')
            ->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
            ->select('plan_topics.*','teaching_plan.*')
            ->where('teaching_plan.course_id','=',$id)
            ->get();
          foreach($delete_topic as $row){
            DB::delete('delete from plan_topics where topic_id = ?',[$row->topic_id]);
          }
          DB::delete('delete from teaching_plan where course_id = ?',[$id]);
          foreach ($TP as $row) {
            $TP = new Teaching_Plan([
                'course_id'         =>  $id,
                'week'              =>  $row->week,
                'tutorial'          =>  $row->tutorial,
                'assessment'        =>  $row->assessment,
                'remarks'           =>  $row->remarks,
            ]);
            $TP->save();
            $tp_id = $TP->tp_id;

            $topic = DB::table('plan_topics')
              ->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
              ->select('plan_topics.*','teaching_plan.*')
              ->where('plan_topics.tp_id','=',$row->tp_id)
              ->get();

            foreach ($topic as $row_topic) {
              $save_topic = new Plan_Topic([
                  'tp_id'               =>  $tp_id,
                  'lecture_topic'       =>  $row_topic->lecture_topic,
                  'lecture_hour'        =>  $row_topic->lecture_hour,
                  'sub_topic'           =>  $row_topic->sub_topic,
              ]);
              $save_topic->save();
            }
          }
          return redirect()->back()->with('success','Weekly Plan Inserted Successfully');
        }else{
          return redirect()->back()->with('Failed','Your last semester of weekly plan is empty.');
        }
      }else{
        return redirect()->back()->with('Failed',$failed);
      }
    }

   	public function storeTP(Request $request, $id)
   	{
   		$TP = DB::table('teaching_plan')
        	->select('teaching_plan.*')
        	->where('teaching_plan.course_id','=',$id)
        	->get();
        if(count($TP)>0){
        	$topic = DB::table('plan_topics')
       			->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
       			->select('plan_topics.*','teaching_plan.*')
       			->where('teaching_plan.course_id','=',$id)
       			->get();
       		foreach($topic as $row){
       			DB::delete('delete from plan_topics where topic_id = ?',[$row->topic_id]);
       		}
       		DB::delete('delete from teaching_plan where course_id = ?',[$id]);
        }

   		$week = $request->get('week');

   		for($i=1;$i<=$week;$i++){
   			$tutorial = $request->get('tutorials_'.$i);
   			$assessment = $request->get('assessments_'.$i);
   			$remark = $request->get('remarks_'.$i);
   			$TP = new Teaching_Plan([
   				'course_id'         =>  $id,
                'week'              =>  $i,
                'tutorial'          =>  $tutorial,
                'assessment'        =>  $assessment,
                'remarks'           =>  $remark,
   			]);
   			$TP->save();

   			$count = $request->get('topic_count_'.$i);
   		 	for($m = 1; $m <= $count; $m++){
   		 		$tp_id = $TP->tp_id;
   		 		$topic = $request->get('lecture_topic_'.$i.'_'.$m);
	   			$hour = $request->get('hour_'.$i.'_'.$m);
	   			$sub_topic = $request->get('sub_topic_'.$i.'_'.$m);
	   			$topic = new Plan_Topic([
	   				'tp_id'               =>  $tp_id,
	                'lecture_topic'       =>  $topic,
	                'lecture_hour'        =>  $hour,
	                'sub_topic'           =>  $sub_topic,
   				]);
   				$topic->save();
   		 	}
   		}
      return redirect()->back()->with('success','Weekly Plan Inserted Successfully');
   	}

   	public function removeTopic(Request $request)
   	{
   		$topic_id = $request->get('value');
   		DB::delete('delete from plan_topics where topic_id = ?',[$topic_id]);
   		return "Success";
   	}

   	public function searchPlan(Request $request)
   	{
   		$course_id = $request->get('course_id');
   		$value     = $request->get('value'); 
       	$result = "";

       	if($value!=""){
       		$TP = DB::table('teaching_plan')
       			->join('plan_topics', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
       			->select('plan_topics.*','teaching_plan.*')
       			->where('teaching_plan.course_id','=',$course_id)
       			->Where(function($query) use ($value) {
                   	$query->orWhere('teaching_plan.tutorial','LIKE','%'.$value.'%')
                       	    ->orWhere('teaching_plan.assessment','LIKE','%'.$value.'%')
                            ->orWhere('teaching_plan.remarks','LIKE','%'.$value.'%')
                            ->orWhere('plan_topics.lecture_topic','LIKE','%'.$value.'%')
                            ->orWhere('plan_topics.sub_topic','LIKE','%'.$value.'%');
                })
                ->groupBy('teaching_plan.tp_id')
       			->get();
	        $result .= '<div class="col-md-12" style="padding:0px;">';
	        $i = 1;
	        if($TP->count()){
	        	foreach($TP as $row){
	        		$result .= '<p class="col-12 align-self-center week" id="'.$i.'" style="padding:10px 10px;font-size: 20px;margin: 0px;">';
                    $result .= '<i class="fa fa-plus" id="icon_'.$i.'" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i> Week '.$row->week.'';
                    $result .= '</p>';
                    $result .= '<div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">';
                    $result .= '<div class="row plan" id="plan_detail_'.$i.'" style="padding: 0px 20px;display:none;">';
                    $result .= '<div class="col-md-9 row" id="topic_list_'.$i.'" style="padding: 0px; margin: 0px;display: inline-block;">';
                    $topic = DB::table('plan_topics')
			       			->select('plan_topics.*')
			       			->where('plan_topics.tp_id','=',$row->tp_id)
			       			->get();
			       	foreach($topic as $row_topic){
			       		$result .= '<div class="col-md-8 topic" style="display: inline-block;height: 50px;">';
	                    $result .= '<div class="row">';
	                    $result .= '<div class="col-1 align-self-center" style="padding: 10px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i></p></div>';
	                    $result .= '<div class="col-11" style="padding-left: 20px;">';
	                    $result .= '<div class="form-group">';
	                    $result .= '<label class="label" style="font-size:12px;padding:0px;">Lecture Topic</label><input type="text" class="form-control" placeholder="Topic" readonly value="'.$row_topic->lecture_topic.'">';
	                    $result .= '</div></div></div></div>';
	                    $result .= '<div class="col-md-3" style="display: inline-block;height: 80px;">';
	                    $result .= '<div class="row">';
	                    $result .= '<div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i></p></div>';
	                    $result .= '<div class="col-11" style="padding-left: 20px;">';
	                    $result .= '<div class="form-group">';
	                    $result .= '<label class="label" style="font-size:12px;padding:0px;">Hour</label><input type="text" class="form-control" placeholder="Time" readonly value="'.$row_topic->lecture_hour.'">';
	                    $result .= '</div></div></div></div>';
	                    $result .= '<div class="col-12" id="topic_sub" style="display: inline-block;">';
	                    $result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-info" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Sub-Topic</label>';
	                    $result .= '<div>'.$row_topic->sub_topic.'</div>';
	                    $result .= '</div><br><br>';
			       	}
			       	$result .= '</div>';
			       	$result .= '<input type="hidden" name="topic_count_'.$i.'" id="topic_count_'.$i.'" value="1">';
			       	$result .= '<div class="col-md-3" style="padding:20px 0px 0px 0px;">';
			       	$result .= '<div class="short-div">';
			       	$result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-file-text" aria-hidden="true" style="font-size: 18px;padding-left:1px;"></i></p><label class="bmd-label-floating">Tutorials</label><div>'.$row->tutorial.'</div>';
			       	$result .= '</div><hr>';
			       	$result .= '<div class="short-div">';
			       	$result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Assessment</label><div>'.$row->assessment.'</div>';
			       	$result .= '</div><hr>';
			       	$result .= '<div class="short-div">';
			       	$result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-exclamation" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Remarks</label><div>'.$row->remarks.'</div>';
			       	$result .= '</div><hr>';
			       	$result .= '</div></div></div>';
                    $i++;
	        	}
	        }else{
	        	$result .= '<div class="col-md-12">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
	        }
       	}else{
       		$TP = DB::table('teaching_plan')
       			->join('plan_topics', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
       			->select('plan_topics.*','teaching_plan.*')
       			->where('teaching_plan.course_id','=',$course_id)
                ->groupBy('teaching_plan.tp_id')
       			->get();
       		$result .= '<div class="col-md-12" style="padding:0px;">';
	        $i = 1;
	        foreach($TP as $row){
	        	$result .= '<p class="col-12 align-self-center week" id="'.$i.'" style="padding:10px 10px;font-size: 20px;margin: 0px;">';
                $result .= '<i class="fa fa-plus" id="icon_'.$i.'" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i> Week '.$i.'';
                $result .= '</p>';
                $result .= '<div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">';
                $result .= '<div class="row plan" id="plan_detail_'.$i.'" style="padding: 0px 20px;display:none;">';
                $result .= '<div class="col-md-9 row" id="topic_list_'.$i.'" style="padding: 0px; margin: 0px;display: inline-block;">';
                $topic = DB::table('plan_topics')
			       			->select('plan_topics.*')
			       			->where('plan_topics.tp_id','=',$row->tp_id)
			       			->get();
			    foreach($topic as $row_topic){
			     	$result .= '<div class="col-md-8 topic" style="display: inline-block;height: 50px;">';
	                $result .= '<div class="row">';
	                $result .= '<div class="col-1 align-self-center" style="padding: 10px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i></p></div>';
	                $result .= '<div class="col-11" style="padding-left: 20px;">';
	                $result .= '<div class="form-group">';
	                $result .= '<label class="label" style="font-size:12px;padding:0px;">Lecture Topic</label><input type="text" class="form-control" placeholder="Topic" readonly value="'.$row_topic->lecture_topic.'">';
	                $result .= '</div></div></div></div>';
	                $result .= '<div class="col-md-3" style="display: inline-block;height: 80px;">';
	                $result .= '<div class="row">';
	                $result .= '<div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i></p></div>';
	                $result .= '<div class="col-11" style="padding-left: 20px;">';
	                $result .= '<div class="form-group">';
	                $result .= '<label class="label" style="font-size:12px;padding:0px;">Hour</label><input type="text" class="form-control" placeholder="Time" readonly value="'.$row_topic->lecture_hour.'">';
	                $result .= '</div></div></div></div>';
	                $result .= '<div class="col-12" id="topic_sub" style="display: inline-block;">';
	                $result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-info" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Sub-Topic</label>';
	                $result .= '<div>'.$row_topic->sub_topic.'</div>';
	                $result .= '</div><br><br>';
			    }
			   	$result .= '</div>';
			    $result .= '<input type="hidden" name="topic_count_'.$i.'" id="topic_count_'.$i.'" value="1">';
			    $result .= '<div class="col-md-3" style="padding:20px 0px 0px 0px;">';
			    $result .= '<div class="short-div">';
			   	$result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-file-text" aria-hidden="true" style="font-size: 18px;padding-left:1px;"></i></p><label class="bmd-label-floating">Tutorials</label><div>'.$row->tutorial.'</div>';
			    $result .= '</div><hr>';
			    $result .= '<div class="short-div">';
			    $result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Assessment</label><div>'.$row->assessment.'</div>';
			    $result .= '</div><hr>';
			    $result .= '<div class="short-div">';
			    $result .= '<p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-exclamation" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Remarks</label><div>'.$row->remarks.'</div>';
			    $result .= '</div><hr>';
			    $result .= '</div></div></div>';
                $i++;
	       	}
	    }
       	return $result;
   	}

    public function createTPAss($id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();
        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();
        if(count($course)>0){
            if(count($TP_Ass)>0){
              return view('dean.TeachingPlan.TeachingPlanAssOld',compact('course','TP_Ass'));
            }else{
              return view('dean.TeachingPlan.TeachingPlanAssCreate',compact('course','TP_Ass'));
            }
        }else{
            return redirect()->back();
        }
    }

    public function createNewTPAss($id){
      $user_id       = auth()->user()->user_id;
      $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
      $faculty_id    = $staff_dean->faculty_id;
      $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();
      $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();
      if(count($course)>0){
        return view('dean.TeachingPlan.TeachingPlanAssCreate',compact('course','TP_Ass'));
      }else{
        return redirect()->back();
      }
    }

    public function createPreviousTPAss($id){
      $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $id)
                 ->get();

      if($course[0]->semester =='A'){
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
                 ->where('courses.course_id', '!=', $id)
                 ->where('courses.status', '=', 'Active')
                 ->where('semesters.semester','=','A')
                 ->orderByDesc('semesters.semester_name')
                 ->get();
        $failed = "The course have not yet open in short semester. Please write down the TP Assessment Method for this course.";
      }else{
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
                 ->where('courses.course_id', '!=', $id)
                 ->where('courses.status', '=', 'Active')
                 ->where('semesters.semester','!=',"A")
                 ->orderByDesc('semesters.semester_name')
                 ->get();
        $failed = "The course have not yet open in long semester. Please write down the TP Assessment Method for this course.";
      }

      if(count($previous)>0){
        $TP_ass = DB::table('tp_assessment_method')
                 ->join('courses','tp_assessment_method.course_id','=','courses.course_id')
                 ->select('courses.*','tp_assessment_method.*')
                 ->where('tp_assessment_method.course_id', '=', $previous[0]->course_id)
                 ->orderBy('tp_assessment_method.am_id')
                 ->get();
        if(count($TP_ass)>0){
          DB::delete('delete from tp_assessment_method where course_id = ?',[$id]);
          foreach($TP_ass as $row){
            $TP_Ass = new TP_Assessment_Method([
              'course_id'         =>  $id,
              'CLO'               =>  $row->CLO,
              'PO'                =>  $row->PO,
              'domain_level'      =>  $row->domain_level,
              'method'            =>  $row->method,
              'assessment'        =>  $row->assessment,
              'markdown'          =>  $row->markdown,
            ]);
            $TP_Ass->save();
          }
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
          return redirect()->back()->with('success','Assessment Method Inserted Successfully');
        }else{
          return redirect()->back()->with('Failed',"Your last semester of Assessment Method is empty.");
        }
      }else{
        return redirect()->back()->with('Failed',$failed);
      }
    }

    public function getSyllabusData(Request $request)
    {
        $id = $request->get('course_id');
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        if(count($course)>0){
            $path = storage_path('private/syllabus/'.$course[0]->syllabus);
            $array = (new syllabusRead)->toArray($path);
            return response()->json($array[0]);
        }else{
            return redirect()->back();
        }      
    }

    public function storeTPAss(Request $request, $id)
    {
      $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();
      if(count($TP_Ass)>0){
        DB::delete('delete from tp_assessment_method where course_id = ?',[$id]);
      }
      $count = $request->get('count');
      $num = $request->get('num');
      for($i = 1; $i<=$count; $i++){
        $CLO = $request->get('CLO_'.$i);
        $PO = $request->get('PO_'.$i);
        $domain_level = $request->get('domain_level_'.$i);
        $method = array();
        $method = $request->get('method_'.$i);
        $assessment = $request->get('assessment_name');
        $assessment_num = $request->get('assessment_num');
        $teaching_method = "";
        if($method!=null){  
          foreach($method as $value){
            $teaching_method .= $value.',';
          }
        }
        $markdown = "";
        for($m = 0; $m<$num; $m++){
          $check = $request->get('assessment_'.$i."_".$m);
          $markdown .= $check.",";
        }
        $TP_Ass = new TP_Assessment_Method([
            'course_id'         =>  $id,
            'CLO'               =>  $CLO,
            'PO'                =>  $PO,
            'domain_level'      =>  $domain_level,
            'method'            =>  $teaching_method,
            'assessment'        =>  $assessment."///".$assessment_num,
            'markdown'          =>  $markdown,
        ]);
        $TP_Ass->save();
      }
      return redirect()->back()->with('success','Assessment Method Inserted Successfully');
    }

    public function createTPCQI($id)
    {
      $user_id       = auth()->user()->user_id;
      $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
      $faculty_id    = $staff_dean->faculty_id;

      $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

      $CQI = DB::table('tp_cqi')
                 ->select('tp_cqi.*')
                 ->where('course_id', '=', $id)
                 ->where('status','=','Active')
                 ->get();
      if(count($course)>0){
        return view('dean.TeachingPlan.TeachingPlanCreateCQI',compact('course','CQI'));
      }else{
        return redirect()->back();
      }
    }

    public function storeTPCQI(Request $request)
    {
      $course_id = $request->get('course_id');
      $count = $request->get('count');
      for($i=1;$i<=$count;$i++){
        $action = $request->get('action_'.$i);
        $plan = $request->get('plan_'.$i);

        $TP_CQI = new TP_CQI([
            'course_id'         =>  $course_id,
            'action'            =>  $action,
            'plan'              =>  $plan,
            'status'            =>  "Active",
        ]);
        $TP_CQI->save();
      }
      return redirect()->back()->with('success','CQI Inserted Successfully');
    }

    public function CQIEdit(Request $request){
        $CQI_id = $request->get('value');
        $CQI = TP_CQI::find($CQI_id);
        return $CQI;
    }

    public function CQIUpdate(Request $request){
      $CQI_id = $request->get('CQI_id');
      $action = $request->get('action');
      $plan   = $request->get('plan');

      $CQI = TP_CQI::where('CQI_id', '=', $CQI_id)->firstOrFail();
      $CQI->action  = $action;
      $CQI->plan  = $plan;
      $CQI->save();
      return redirect()->back()->with('success','CQI Edited Successfully');
    } 

    public function removeActive($id){
      $CQI = TP_CQI::where('CQI_id', '=', $id)->firstOrFail();
      $CQI->status  = "Remove";
      $CQI->save();
      return redirect()->back()->with('success','Remove Successfully');
    }


  public function TPDownload($id){
      $user_id       = auth()->user()->user_id;
      $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
      $faculty_id    = $staff_dean->faculty_id;
      $department_id = $staff_dean->department_id;

      $course = DB::table('courses')
                   ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                   ->join('programmes','subjects.programme_id','=','programmes.programme_id')
                   ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                   ->join('staffs','staffs.id','=','courses.lecturer')
                   ->join('users','staffs.user_id','=','users.user_id')
                   ->select('courses.*','subjects.*','semesters.*','programmes.*','staffs.*','users.*')
                   ->where('lecturer', '=', $staff_dean->id)
                   ->where('course_id', '=', $id)
                   ->get();

      $TP = DB::table('teaching_plan')
            ->select('teaching_plan.*')
            ->where('teaching_plan.course_id','=',$id)
            ->get();

      $TP_Ass = DB::table('tp_assessment_method')
                ->select('tp_assessment_method.*')
                ->where('course_id', '=', $id)
                ->get();

      $TP_CQI = DB::table('tp_cqi')
                ->select('tp_cqi.*')
                ->where('course_id', '=', $id)
                ->where('status','=','Active')
                ->get();

      $action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderByDesc('action_id')
                  ->get();

        $path = storage_path('private/syllabus/'.$course[0]->syllabus);
        $array = (new syllabusRead)->toArray($path);
        $CLO = "";
        for($i=0;$i<(count($array[0]));$i++){
          if($array[0][$i][2]=="Synopsis :"){
            $synopsis = str_replace("•", "<w:br/>•", $array[0][$i][3]);
          }
          $str = strval($array[0][$i][2]);
          if((str_contains($str, 'CLO'))&&($array[0][$i][1]==null)&&($array[0][$i][3]!=null)&&($array[0][$i][15]==null)){
            if($CLO == ""){
              $CLO .= $array[0][$i][2].": ".$array[0][$i][3];
            }else{
          $CLO .= "<w:br/>".$array[0][$i][2].": ".$array[0][$i][3];
            }
          }
          if((str_contains($str, 'References'))&&($array[0][$i][1]!=null)&&($array[0][$i][8]!=null)){
            $references = str_replace("• Additional", "<w:br/>• Additional", $array[0][$i][8]);
          }
        }

      $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // New section
    $section = $phpWord->addSection(array('marginLeft' => 700, 'marginRight' => 700,'marginTop' => 1000, 'marginBottom' => 1000));
    $header = $section->addHeader();
    $styleTable = array('borderSize' => 6, 'borderColor' => 'black', 'cellMargin' => 10);
    $phpWord->addTableStyle('header', $styleTable);
    $table = $header->addTable('header');
    $cellRowSpan = array('vMerge' => 'restart','valign' => 'center');
    $cellRowContinue = array('vMerge' => 'continue','valign' => 'center');
    $cellColSpan = array('gridSpan' => 2);
    $noSpaceAndCenter = array('spaceAfter' => 0,'align'=>'center');
    $table->addRow(1);
    $table->addCell(4000, $cellRowSpan)->addImage('image/logo.png', array('width' => 132, 'height' => 40),$noSpaceAndCenter);
    $table->addCell(5000, $cellRowSpan)->addText("",$noSpaceAndCenter);
    $table->addCell(2200)->addText("Doc. No.",null,$noSpaceAndCenter);
    $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

    $table->addRow(1);
    $table->addCell(null, $cellRowContinue);
    $table->addCell(null, $cellRowContinue);
    $table->addCell(2200)->addText("Rev. No.",null,$noSpaceAndCenter);
    $table->addCell(2500)->addText("00",null,$noSpaceAndCenter);

    $table->addRow(1);
    $table->addCell(null, $cellRowContinue);
    $table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('TEACHING PLAN'),null,$noSpaceAndCenter);
    $table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
    $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

    $table->addRow(1);
    $table->addCell(null, $cellRowContinue);
    $table->addCell(null, $cellRowContinue);
    $table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
    $table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

    $textrun = $header->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);

    $teaching_plan_full_title = $section->addText('TEACHING PLAN',array('bold' => true),$noSpaceAndCenter);

    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);

    $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
    $phpWord->addTableStyle('title', $styleTable);
    $title = $section->addTable('title');
    // $section->addTextBreak(1);
    $title->addRow();
    $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part A : Course Information',array('bold' => true),$noSpaceAndCenter);

    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);


    $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
    $fontStyle = array('bold' => true);
    $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
    $phpWord->addTableStyle('Course Table', $styleTable);
    $course_table = $section->addTable('Course Table');
    $styleCell = array('valign' => 'center');
    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('1.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Course Code &amp; Course Title: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($course[0]->subject_code." : ".$course[0]->subject_name, null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('2.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Year of Study (Programme): ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText('Year 1 and Year 2 ('.$course[0]->programme_name.')' , null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('3.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Credit Hour: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($course[0]->credit, null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('4.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Lecturer: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($course[0]->name."( ".$course[0]->staff_id." )", null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('5.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Tutor: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($course[0]->name."( ".$course[0]->staff_id." )", null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('6.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Year and Trimester: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($course[0]->semester_name, null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('7.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Synopsis: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($synopsis, null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('8.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('Course Learning Outcomes (CLO): ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($CLO, null, $noSpaceAndLeft);

    $course_table->addRow(1);
    $course_table->addCell(500,$styleCell)->addText('9.',null, $noSpaceAndLeft);
    $course_table->addCell(2000,$styleCell)->addText('References: ', null, $noSpaceAndLeft);
    $course_table->addCell(10000,$styleCell)->addText($references, null, $noSpaceAndLeft);

    $section->addPageBreak();
    
    $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
    $phpWord->addTableStyle('title', $styleTable);
    $title = $section->addTable('title');
    // $section->addTextBreak(1);
    $title->addRow();
    $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part B : Methods of Assessment',array('bold' => true),$noSpaceAndCenter);

    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);

        $all_assessment = explode('///',$TP_Ass[0]->assessment);
        $assessment = explode(',',$all_assessment[0]);
        $assessment_num = explode(',',$all_assessment[1]);

    $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
    $fontStyle = array('bold' => true);
    $phpWord->addTableStyle('Fancy Table', $styleTable);
    $table = $section->addTable('Fancy Table');
    $cellRowSpan = array('vMerge' => 'restart','valign' => 'center','bgColor' => 'cccccc');
    $cellRowContinue = array('vMerge' => 'continue','valign' => 'center','bgColor' => 'cccccc');
    $cellColSpan = array('gridSpan' => (count($assessment)-1),'valign' => 'center','bgColor' => 'cccccc');
    $table->addRow(1);
    $table->addCell(500,$cellRowSpan)->addText('NO',array('bold' => true), $noSpaceAndCenter);
    $table->addCell(800,$cellRowSpan)->addText('CO', $fontStyle, $noSpaceAndCenter);
    $table->addCell(500,$cellRowSpan)->addText('Programme Outcomes (PO)', $fontStyle, $noSpaceAndCenter);
    $table->addCell(500,$cellRowSpan)->addText('Domain &amp; Taxonomy Level', $fontStyle, $noSpaceAndCenter);
    $table->addCell(500,$cellRowSpan)->addText('Teaching Methods', $fontStyle, $noSpaceAndCenter);
    $table->addCell(8000,$cellColSpan)->addText('Assessment Methods &amp; Mark Breakdown', $fontStyle, $noSpaceAndCenter);

    $table->addRow(1);
    $table->addCell(500,$cellRowContinue);
    $table->addCell(800,$cellRowContinue);
    $table->addCell(1000,$cellRowContinue);
    $table->addCell(1000,$cellRowContinue);
    $table->addCell(1000,$cellRowContinue);
    for($m = 0;$m<(count($assessment)-1);$m++){
      $table->addCell(2000,array('bgColor' => 'cccccc'))->addText($assessment[$m], $fontStyle, $noSpaceAndCenter);
    }
    

    $table->addRow(1);
    $table->addCell(500,$cellRowContinue);
    $table->addCell(800,$cellRowContinue);
    $table->addCell(1000,$cellRowContinue);
    $table->addCell(1000,$cellRowContinue);
    $table->addCell(1000,$cellRowContinue);
    for($n = 0;$n<(count($assessment_num)-1);$n++){
      $table->addCell(2000,array('bgColor' => 'cccccc'))->addText($assessment_num[$n]."%", $fontStyle, $noSpaceAndCenter);
    }

    $num = 1;
    foreach($TP_Ass as $row){
      $table->addRow(1);
      $table->addCell(500)->addText($num,null, $noSpaceAndCenter);
      $table->addCell(800)->addText($row->CLO, null, $noSpaceAndCenter);
      $table->addCell(1000)->addText($row->PO, null, $noSpaceAndCenter);
      $table->addCell(1000)->addText($row->domain_level, null, $noSpaceAndCenter);
      $method = str_replace(",", ",<w:br/>", $row->method);
      $table->addCell(1000)->addText($method, null, $noSpaceAndCenter);     
      $check = explode(',',$row->markdown);
      for($c = 0; $c<=($n-1);$c++){
        if($check[$c]!=""){
          $table->addCell(2000)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(2000)->addText('N', array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
        }
      }
      $num++;
    }

    $cellColSpanFull = array('gridSpan' => (5+count($assessment)-1),'valign' => 'center','bgColor' => '#d9d9d9');
    $table->addRow(1);
    $table->addCell(12000,$cellColSpanFull)->addText("*Domain -- Affective (A), Cognitive (C), Psychomotor (P); Taxonomy Level - A(Level 1-5), C(Level 1-6), P(Level 1-5).*<w:br/>*All COs must be assessed by at least one assessment method (ensure that the only assessment method is not an optional choice).*<w:br/>*Individual breakdown of marks for an assessment method (i.e. one assessment question / part mapped to only one CO) is not necessary in the teaching plan. Individual breakdown of marks is only required when preparing the assessment moderation form.*", null, $noSpaceAndLeft);

    $section->addPageBreak();

    $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
    $phpWord->addTableStyle('title', $styleTable);
    $title = $section->addTable('title');
    $title->addRow();
    $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part C : Continual Quality Improvement (CQI)',array('bold' => true),$noSpaceAndCenter);
    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);

    $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
    $fontStyle = array('bold' => true);
    $phpWord->addTableStyle('CQI table', $styleTable);
    $table = $section->addTable('CQI table');
    $styleCell = array('valign' => 'center');
    $table->addRow(1);
    $table->addCell(600,$styleCell)->addText('No',array('bold' => true), $noSpaceAndCenter);
    $table->addCell(6000,$styleCell)->addText('Proposed Improvement Action(s)<w:br/>(from previous trimester Course Report)', $fontStyle, $noSpaceAndCenter);
    $table->addCell(6000,$styleCell)->addText('Plan for this Trimester<w:br/>(action(s) must be shown in Part D, if applicable)<w:br/>(to be transferred to this trimester Course Report)', $fontStyle, $noSpaceAndCenter);

    $num = 1;
    foreach($TP_CQI as $row){
      $table->addRow(5);
      $table->addCell(600,$styleCell)->addText($num,null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText($row->action, null, $noSpaceAndLeft);
      $table->addCell(6000,$styleCell)->addText($row->plan, null, $noSpaceAndLeft);
      $num++;
    }

    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);
    $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
    $phpWord->addTableStyle('title', $styleTable);
    $title = $section->addTable('title');
    $title->addRow();
    $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part D : Weekly Plan',array('bold' => true),$noSpaceAndCenter);
    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);
    $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
    $fontStyle = array('bold' => true);
    $phpWord->addTableStyle('Fancy Table', $styleTable);
    $table = $section->addTable('Fancy Table');
    $styleCell = array('valign' => 'center');
    $table->addRow(1,array('tblHeader' => true));
    $table->addCell(600,$styleCell)->addText('Week',array('bold' => true), $noSpaceAndCenter);
    $table->addCell(5200,$styleCell)->addText('Lecture Topic <w:br/> (including sub-topics)', $fontStyle, $noSpaceAndCenter);
    $table->addCell(800,$styleCell)->addText('Lecture <w:br/> (F2F) Hour', $fontStyle, $noSpaceAndCenter);
    $table->addCell(1500,$styleCell)->addText('Tutorial / Practical', $fontStyle, $noSpaceAndCenter);
    $table->addCell(1800,$styleCell)->addText('Assessment', $fontStyle, $noSpaceAndCenter);
    $table->addCell(2000,$styleCell)->addText('Remarks <w:br/> (CQI Action / Activity)', $fontStyle, $noSpaceAndCenter);


    foreach($TP as $row){
      $cellRowSpan = array('vMerge' => 'restart');
      $cellRowContinue = array('vMerge' => 'continue','valign' => 'center');
      $topic = DB::table('plan_topics')
            ->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
            ->select('plan_topics.*','teaching_plan.*')
            ->where('plan_topics.tp_id','=',$row->tp_id)
            ->get();
          $i = 0;
          foreach($topic as $row_topic){
            $table->addRow(null);
            if($i==0){
              $table->addCell(600,$cellRowSpan)->addText($row->week,array( 'bold'=>true ), $noSpaceAndCenter);
            }else{
              $table->addCell(null,$cellRowContinue);
            }
        $L_topic = $table->addCell(5200);
        $lecture_topic = "";
        if($row_topic->lecture_topic!=""){
          $lecture_topic = explode('///',$row_topic->lecture_topic);
          \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$lecture_topic[1].'</b>',false);
        }else{
          \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$row_topic->lecture_topic.'</b>',false);
        }
        $html = str_replace("<br>","<br/>",$row_topic->sub_topic);

        \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,$html,false);
        $table->addCell(800)->addText($row_topic->lecture_hour,null,$noSpaceAndCenter);
        if($i==0){
          $tutorial = $table->addCell(1500,$cellRowSpan);
          $html_t = str_replace("<br>","<br/>",$row->tutorial);
          \PhpOffice\PhpWord\Shared\Html::addHtml($tutorial,"<span style='text-align:center'>".$html_t."</span>",false);
          $assessment = $table->addCell(1800,$cellRowSpan);
          $html_a = str_replace("<br>","<br/>",$row->assessment);
          \PhpOffice\PhpWord\Shared\Html::addHtml($assessment,"<span style='text-align:center'>".$html_a."</span>",false);
          $remark = $table->addCell(2000,$cellRowSpan);
          $html_r = str_replace("<br>","<br/>",$row->remarks);
          \PhpOffice\PhpWord\Shared\Html::addHtml($remark,"<span style='text-align:center'>".$html_r."</span>",false);
        }else{
          $table->addCell(null,$cellRowContinue);
          $table->addCell(null,$cellRowContinue);
          $table->addCell(null,$cellRowContinue);
        }
        $i++;
          }
    }

    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);
    $textrun = $section->addTextRun();
    $textrun->addText("",null,$noSpaceAndCenter);

    $section->addText('This Teaching Plan is: ');
    $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
    $fontStyle = array('bold' => true);
    $phpWord->addTableStyle('Sign Table', $styleTable);
    $table = $section->addTable('Sign Table');
    $styleCell = array('valign' => 'center');
    $Moderator = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id', '=', $course[0]->moderator)
                 ->get();

    $verified_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'HoD')
                 ->where('staffs.department_id','=',$department_id)
                 ->get();

    // $verified_by = DB::table('staffs')
    //              ->join('users','staffs.user_id','=','users.user_id')
    //              ->select('staffs.*','users.*')
    //              ->where('staffs.id', '=', $course[0]->verified_by)
    //              ->get();

    $table->addRow(1);
    if($action[0]->prepared_date!=NULL){
      if($course[0]->staff_sign!=NULL){
        $s_p = storage_path('/private/staffSign/'.$course[0]->staff_sign);
        $table->addCell(4000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
      }else{
        $table->addCell(4000,$styleCell)->addText($course[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
      }
    }else{
      $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
    }

    if($action[0]->verified_date!=NULL){
      if($Moderator[0]->staff_sign!=NULL){
        $s_m = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
        $table->addCell(4000)->addImage($s_m,array('width'=>80, 'height'=>40, 'align'=>'center'));
      }else{
        $table->addCell(4000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
      }
    }else{
      $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
    }

    if($action[0]->approved_date!=NULL){
      if($verified_by[0]->staff_sign!=NULL){
        $s_v = storage_path('/private/staffSign/'.$verified_by[0]->staff_sign);
        $table->addCell(4000)->addImage($s_v,array('width'=>80, 'height'=>40, 'align'=>'center'));
      }else{
        $table->addCell(4000,$styleCell)->addText($verified_by[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
      }
    }else{
      $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
    }

    $table->addRow(1);
    $table->addCell(4000)->addText('Prepared By : '.$course[0]->name.'<w:br/>Course Coordinator',null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Moderated By: '.$Moderator[0]->name.'<w:br/>Moderator', null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Approved By : '.$verified_by[0]->name.'<w:br/>'.$verified_by[0]->position, null, $noSpaceAndLeft);

    $table->addRow(1);
    $table->addCell(4000)->addText('Date: '.$action[0]->prepared_date,null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Date: '.$action[0]->verified_date,null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Date: '.$action[0]->approved_date,null, $noSpaceAndLeft);

    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
    return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
  }

  public function TPSubmitAction($id)
  {
    $user_id       = auth()->user()->user_id;
    $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    $faculty_id    = $staff_dean->faculty_id;
    $course = DB::table('courses')
              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
              ->select('courses.*','subjects.*','semesters.*')
              ->where('lecturer', '=', $staff_dean->id)
              ->where('course_id', '=', $id)
              ->get();
    if(count($course)>0){
      $action = new Action_V_A([
        'course_id'     => $id,
        'action_type'   => "TP",
        'status'        => "Waiting For Verified",
        'for_who'       => "Moderator",
        'prepared_date' => date("Y-n-j"),
      ]);
      $action->save();
      return redirect()->back()->with('success','Teaching Plan Submitted to Moderator Successfully');
    }else{
      return redirect()->back();
    }
  }
}
