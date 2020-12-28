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


class NotificationController extends Controller
{
	public function getNum(Request $request)
	{
		$user_id       = $request->get('value');
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course_count  = 0;

        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        foreach($course as $row){
        	$course_tp_action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as action_status')
                  ->where('courses.course_id','=',$row->course_id)
                  ->where('courses.status','=','Active')
        		  ->orderByDesc('action_v_a.action_id')
                  ->first();
            if($course_tp_action!=""){
            	if($course_tp_action->action_status=="Rejected"){
	            	$course_count++;
	            }
            }
  			
            $course_CA_action = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as action_status')
                  ->where('courses.course_id','=',$row->course_id)
                  ->where('courses.status','=','Active')
        		  ->orderByDesc('actionca_v_a.actionCA_id')
                  ->first();
            if($course_CA_action!=""){
	            if($course_CA_action->action_status=="Rejected"||$course_CA_action->action_status=="Waiting For Rectification"){
	            	$course_count++;
	            }
	        }
            $course_FA_action = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionfa_v_a.status as action_status')
                  ->where('courses.course_id','=',$row->course_id)
                  ->where('courses.status','=','Active')
        		  ->orderByDesc('actionfa_v_a.actionFA_id')
                  ->first();
            if($course_FA_action!=""){
	            if($course_FA_action->action_status=="Rejected"||$course_FA_action->action_status=="Waiting For Rectification"){
            		$course_count++;
            	}
	        }
        }

        $action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                  ->where('courses.semester','=',$semester_id)
                  ->where('courses.moderator', '=', $staff_dean->id)
                  ->where('courses.status','=','Active')
                  ->where('action_v_a.status','=','Waiting For Verified')
                  ->where('action_v_a.for_who','=','Moderator')
                  ->get();

        $action2 = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                  ->where('courses.semester','=',$semester_id)
                  ->where('courses.moderator', '=', $staff_dean->id)
                  ->where('courses.status','=','Active')
                  ->where('actionca_v_a.status','=','Waiting For Moderation')
                  ->where('actionca_v_a.for_who','=','Moderator')
                  ->get();

        $action3 = DB::table('actionfa_v_a')
                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                  ->where('courses.semester','=',$semester_id)
                  ->where('courses.moderator', '=', $staff_dean->id)
                  ->where('courses.status','=','Active')
                  ->where('actionfa_v_a.status','=','Waiting For Moderation')
                  ->where('actionfa_v_a.for_who','=','Moderator')
                  ->get();

        if(auth()->user()->position=="Dean"){
        	$FA_count = DB::table('actionfa_v_a')
                    ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->where('courses.status','=','Active')
                    ->where('actionfa_v_a.status','=','Waiting For Approve')
                    ->where('actionfa_v_a.for_who','=','Dean')
                    ->get();
            $num_reviewer = count($FA_count);
        }else if(auth()->user()->position=="HoD"){
        	$TP_count = DB::table('action_v_a')
                      ->join('courses','courses.course_id','=','action_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->where('action_v_a.status','=','Waiting For Approved')
                      ->where('action_v_a.for_who','=','HOD')
                      ->get();

            $CA_count = DB::table('actionca_v_a')
                      ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->where('actionca_v_a.status','=','Waiting For Verified')
                      ->where('actionca_v_a.for_who','=','HOD')
                      ->get();

            $FA_count = DB::table('actionfa_v_a')
                      ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                      ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                      ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                      ->join('staffs', 'staffs.id','=','courses.lecturer')
                      ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                      ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*')
                      ->where('courses.semester','=',$semester_id)
                      ->where('departments.department_id','=',$department_id)
                      ->where('courses.status','=','Active')
                      ->where('actionfa_v_a.status','=','Waiting For Verified')
                      ->where('actionfa_v_a.for_who','=','HOD')
                      ->get();

            $num_reviewer = count($TP_count)+count($CA_count)+count($FA_count);
        }

        $num_moderator = count($action)+count($action2)+count($action3);
        return $num_moderator."/".$num_reviewer.'/'.$course_count;
	}

	public static function getTP_Num($id,$place)
	{
		$course_count  = 0;

		$course_tp_action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as action_status')
                  ->where('courses.course_id','=',$id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('action_v_a.action_id')
                  ->first();

		if($place=="course"){
        	if($course_tp_action!=""){
	            if($course_tp_action->action_status=="Rejected"){
	                $course_count++;
	            }
	        }
        }else if($place=="Moderator"){
        	if($course_tp_action!=""){
	            if($course_tp_action->action_status=="Waiting For Verified"){
	                $course_count++;
	            }
	        }
        }else if($place=="Reviewer"){
          if(auth()->user()->position=="HoD"){
            if($course_tp_action!=""){
              if($course_tp_action->action_status=="Waiting For Approved"){
                  $course_count++;
              }
            }
          }
        }
        
        return $course_count;
	}

	public static function getCA_Num($id,$place)
	{
		$course_count  = 0;
		$course_CA_action = DB::table('actionca_v_a')
	                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
	                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
	                  ->join('staffs', 'staffs.id','=','courses.lecturer')
	                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
	                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as action_status')
	                  ->where('courses.course_id','=',$id)
	                  ->where('courses.status','=','Active')
	                  ->orderByDesc('actionca_v_a.actionCA_id')
	                  ->first();
		if($place=="course"){
			if($course_CA_action!=""){
	       if($course_CA_action->action_status=="Rejected"||$course_CA_action->action_status=="Waiting For Rectification"){
	          $course_count++;
	       }
	     }
	    }else if($place=="Moderator"){
	    	if($course_CA_action!=""){
	         if($course_CA_action->action_status=="Waiting For Moderation"){
	           $course_count++;
	         }
	      }
	    }else if($place=="Reviewer"){
        if(auth()->user()->position=="HoD"){
          if($course_CA_action!=""){
            if($course_CA_action->action_status=="Waiting For Verified"){
                $course_count++;
            }
          }
        }
      }
      return $course_count;
	}

	public static function getFA_Num($id,$place)
	{
		$course_count  = 0;
		$course_FA_action = DB::table('actionfa_v_a')
	                  ->join('courses','courses.course_id','=','actionfa_v_a.course_id')
	                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
	                  ->join('staffs', 'staffs.id','=','courses.lecturer')
	                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
	                  ->select('actionfa_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionfa_v_a.status as action_status')
	                  ->where('courses.course_id','=',$id)
	                  ->where('courses.status','=','Active')
	                  ->orderByDesc('actionfa_v_a.actionFA_id')
	                  ->first();

		if($place=="course"){
			if($course_FA_action!=""){
	            if($course_FA_action->action_status=="Rejected"||$course_FA_action->action_status=="Waiting For Rectification"){
	                $course_count++;
	            }
	        }
        }else if($place=="Moderator"){
        	if($course_FA_action!=""){
	            if($course_FA_action->action_status=="Waiting For Moderation"){
	                $course_count++;
	            }
	        }
        }else if($place=="Reviewer"){
          if(auth()->user()->position=="HoD"){
            if($course_FA_action!=""){
              if($course_FA_action->action_status=="Waiting For Verified"){
                $course_count++;
              }
            }
          }else if(auth()->user()->position=="Dean"){
            if($course_FA_action->action_status=="Waiting For Approve"){
                $course_count++;
            }
          }
        }
        return $course_count;
	}
}