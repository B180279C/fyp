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

class D_StudentListController extends Controller
{
	public function DeanStudent($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

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
            return view('dean.Reviewer.Student_List.D_StudentList',compact('course','id','batch','assign_student'));
        }else{
            return redirect()->route('home');
        }
	}

	public function searchDeanStudent(Request $request)
	{
		$value = $request->get('value');
        $course_id = $request->get('course_id');
        $result = "";
        if($value!=""){
        	$batch = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->Where(function($query) use ($value) {
                          $query->orWhere('students.student_id','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%')
                            ->orWhere('students.batch','LIKE','%'.$value.'%');
                    })
                    ->groupBy('students.batch')
                    ->get();
           $assign_student = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->Where(function($query) use ($value) {
                          $query->orWhere('students.student_id','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%')
                            ->orWhere('students.batch','LIKE','%'.$value.'%');
                    })
                    ->get();

            if(count($batch)>0){
            	$i=0;
            	foreach($batch as $row_batch){
            		$result .='<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .='<div class="col-12 row" style="padding:15px 10px 5px 10px;margin: 0px;">';
                    $result .='<h5 class="group plus" id="'.$i.'">'.$row_batch->batch.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .='</div>';
                    $result .='<div id="student_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px 0px 5px 0px;">';
            		foreach($assign_student as $row){
            			if($row->batch == $row_batch->batch){
		                    $result .='<div class="col-md-4" style="margin: 0px;padding:2px;">';
	                        $result .='<center>';
	                        $result .='<a href="" class="row" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;margin: 0px;">';
	                        $result .='<div class="col-12" style="color: #0d2f81;padding: 10px;">';
	                        $result .='<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>'.$row->name.' ( '.$row->student_id.')</b></p>';
	                        $result .='</div>';
	                        $result .='</a>';
	                        $result .='</center>';
	                      	$result .='</div>';
                  		}
                	}
                	$i++;
                	$result .='</div></div>';
            	}
            }else{
            	$result .= '<div class="col-md-12">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
        	$batch = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->groupBy('students.batch')
                    ->get();
            $assign_student = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
            $i=0;
            foreach($batch as $row_batch){
            	$result .='<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                $result .='<div class="col-12 row" style="padding:15px 10px 5px 10px;margin: 0px;">';
                $result .='<h5 class="group plus" id="'.$i.'">'.$row_batch->batch.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                $result .='</div>';
                $result .='<div id="student_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px 0px 5px 0px;">';
          		foreach($assign_student as $row){
            		if($row->batch == $row_batch->batch){
		                $result .='<div class="col-md-4" style="margin: 0px;padding:2px;">';
	                    $result .='<center>';
	                    $result .='<a href="" class="row" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;margin: 0px;">';
	                    $result .='<div class="col-12" style="color: #0d2f81;padding: 10px;">';
	                    $result .='<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>'.$row->name.' ( '.$row->student_id.')</b></p>';
	                    $result .='</div>';
	                    $result .='</a>';
	                    $result .='</center>';
	                   	$result .='</div>';
                  	}
                }
                $i++;
                $result .='</div></div>';
            }
        }
        return $result;
	}
}