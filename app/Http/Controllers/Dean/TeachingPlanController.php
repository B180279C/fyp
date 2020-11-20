<?php

namespace App\Http\Controllers\Dean;

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

class TeachingPlanController extends Controller
{
   	public function viewTeachingPlan($id){
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
        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();
        if(count($course)>0){
            return view('dean.TeachingPlan.viewTeachingPlan',compact('course','TP','topic','TP_Ass'));
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
      $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();
      return view('dean.TeachingPlan.viewTeachingPlan',compact('course','TP','topic','TP_Ass'))->with('success','Assessment Method Inserted Successfully');
    }
}
