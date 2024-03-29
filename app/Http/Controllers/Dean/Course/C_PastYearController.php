<?php

namespace App\Http\Controllers\Dean\Course;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Student;
use App\Staff;
use App\Assessments;
use App\AssessmentList;
use App\AssessmentResultStudent;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class C_PastYearController extends Controller
{
    public function PastYearAssessment($id)
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

        $previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessments','courses.course_id','=','assessments.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.PastYear.viewAssessment',compact('course','previous_semester'));
        }else{
            return redirect()->back();
        }
    }

    public function searchAssessment(Request $request)
    {
    	$value         = $request->get('value');
        $course_id     = $request->get('course_id');

        $subjects = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $result = "";

        if($value!=""){
        	$data_name = DB::table('assessments')
                    ->join('courses','courses.course_id','=','assessments.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('subjects.*','courses.*','semesters.*','assessments.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessments.assessment_name','LIKE','%'.$value.'%')
                        	->orWhere('assessments.assessment','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
                    })
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
           	if(count($data_name)>0) {
           		$result .= '<input type="hidden" id="data" value="name">';
           		foreach($data_name as $ass_row_name){
           			$result .= '<div class="col-12 row align-self-center" id="course_list">';
	                $result .= '<div class="col-12 row align-self-center">';
	                $result .= '<div class="checkbox_style align-self-center">';
	                $result .= '<input type="checkbox" value="'.$ass_row_name->ass_id.'" class="group_q group_download">';
	                $result .= '</div>';
	                $result .= '<a href="'.$character.'/CourseList/PastYear/assessment/'.$course_id.'/list/'.$ass_row_name->ass_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
	                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
	                $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
	                $result .= '</div>';
	                $result .= '<div class="col-10" id="assessment_name">';
	                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$ass_row_name->semester_name." : ".$ass_row_name->assessment_name.'</b></p>';
	                $result .= '</div>';
	          		$result .= '</a>';
	                $result .= '</div>';
	                $result .= '</div>';
           		}
           	}else{
           		$data_word = DB::table('assessment_list')
           			->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->join('courses','courses.course_id','=','assessments.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('subjects.*','courses.*','semesters.*','assessments.*','assessment_list.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_list.ass_word','LIKE','%'.$value.'%');
                    })
                    ->where('courses.status', '=', 'Active')
                    ->where('assessment_list.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->get();

                if(count($data_word)>0) {
                	$result .= '<input type="hidden" id="data" value="word">';
                	foreach($data_word as $ass_row_word){
                		$result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-9 row align-self-center" >';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$ass_row_word->ass_li_id.'" class="group_q group_download">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/CourseList/PastYear/images/assessment/'.$course_id."-".$ass_row_word->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$ass_row_word->semester_name.' : '.$ass_row_word->assessment_name.' / '.$ass_row_word->ass_type.' / '.$ass_row_word->ass_name.' <br> <a href='.$character."/CourseList/PastYear/assessment/view/whole_paper/".$course_id."-".$ass_row_word->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                        $result .= '</div>';
                       	$result .= '<div class="col-10" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$ass_row_word->semester_name." : ".$ass_row_word->assessment_name." / ".$ass_row_word->ass_type." / ".$ass_row_word->ass_name.'</b></p>';
                        $result .= '</div>';
                       	$result .= '</a>';
                        $result .= '</div>';
                        $result .= '<div class="col-3" id="course_action_two">';
	                    $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$ass_row_word->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
	                    $result .= '</div>';
                        $result .= '</div>';
                	}
                }else{
                	$result .= '<div class="col-md-12" style="position:relative;top:5px;left:5px">';
	                $result .= '<p>Not Found</p>';
	                $result .= '</div>';
                }    
           	}
        }else{
        	$previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessments','courses.course_id','=','assessments.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();
           	foreach($previous_semester as $row){
           		$result .= '<div class="col-12 row align-self-center" id="course_list">';
	            $result .= '<div class="col-8 row align-self-center">';
	            $result .= '<div class="checkbox_style align-self-center">';
	            $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
	            $result .= '</div>';
	            $result .= '<a href="'.$character.'/CourseList/PastYear/assessment/'.$course_id.'/assessment_name/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
	            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
	            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
	            $result .= '</div>';
	            $result .= '<div class="col-10" id="course_name">';
	            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->semester_name.'</b></p>';
	            $result .= '</div>';
	        	$result .= '</a>';
	        	$result .= '</div>';
	        	$result .= '<div class="col-4 row align-self-center" id="lecturer_name">';
                $result .= '<p style="width: 100%;">Lecturer : '.$row->name.'</p>';
                $result .= '</div>';
	            $result .= '</div>';
	            $result .= '</div>';
           	}
        }
        return $result;
    }

    public function PastYearAssessmentName($id,$course_id)
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

       	$previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $assessments = DB::table('assessments')
                 ->select('assessments.*')
                 ->where('assessments.course_id', '=', $course_id)
                 ->where('status', '=', 'Active')
                 ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.PastYear.viewAssessmentName',compact('course','previous','assessments','id'));
        }else{
            return redirect()->back();
        }
    }

    public function searchAssessmentName(Request $request)
    {
    	$value         = $request->get('value');
        $course_id     = $request->get('course_id');
        $original_id   = $request->get('original_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $subjects = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $result = "";
        if($value!=""){
        	$data_word = DB::table('assessment_list')
           			->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->join('courses','courses.course_id','=','assessments.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('courses.*','semesters.*','assessments.*','assessment_list.*')
                    ->where('courses.course_id','=',$course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_list.ass_word','LIKE','%'.$value.'%');
                    })
                    ->where('courses.status', '=', 'Active')
                    ->where('assessment_list.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->get();

            if(count($data_word)>0) {
                $result .= '<input type="hidden" id="data" value="word">';
                foreach($data_word as $ass_row_word){
                	$result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-9 row align-self-center" >';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$ass_row_word->ass_li_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/CourseList/PastYear/images/assessment/'.$original_id."-".$ass_row_word->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$ass_row_word->semester_name.' : '.$ass_row_word->assessment_name.' / '.$ass_row_word->ass_type.' / '.$ass_row_word->ass_name.' <br> <a href='.$character."/CourseList/PastYear/assessment/view/whole_paper/".$original_id."-".$ass_row_word->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$ass_row_word->assessment_name." / ".$ass_row_word->ass_type." / ".$ass_row_word->ass_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-3" id="course_action_two">';
                    $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$ass_row_word->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                    $result .= '</div>';
                    $result .= '</div>';
               	}
           	}else{
               	$result .= '<div class="col-md-12" style="position:relative;top:10px;">';
	            $result .= '<p>Not Found</p>';
	            $result .= '</div>';
            }
        }else{
        	$assessments = DB::table('assessments')
                 ->select('assessments.*')
                 ->where('assessments.course_id', '=', $course_id)
                 ->where('status', '=', 'Active')
                 ->get();

            foreach($assessments as $row){
            	$result .= '<div class="col-12 row align-self-center" id="course_list">';
	            $result .= '<div class="col-12 row align-self-center">';
	            $result .= '<div class="checkbox_style align-self-center">';
	            $result .= '<input type="checkbox" value="'.$row->ass_id.'" class="group_download">';
	            $result .= '</div>';
	            $result .= '<a href="'.$character.'/CourseList/PastYear/assessment/'.$course_id.'/list/'.$row->ass_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
	            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
	            $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
	            $result .= '</div>';
	            $result .= '<div class="col-10" id="assessment_name">';
	            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->assessment_name.'</b></p>';
	            $result .= '</div>';
	        	$result .= '</a>';
	            $result .= '</div>';
	            $result .= '</div>';
            }
        }
        return $result;
    }

    public function PastYearAssessmentList($id,$ass_id)
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

        $assessments = Assessments::where('ass_id','=',$ass_id)->firstOrFail();
		$course_id = $assessments->course_id;

		$previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $group_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('ass_id', '=', $ass_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('ass_type')
                    ->get();

        $assessment_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('ass_id', '=', $ass_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_list.ass_type')
                    ->orderBy('assessment_list.ass_name')
                    ->orderBy('assessment_list.ass_li_id')
                    ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.PastYear.viewAssessmentList',compact('course','assessments','previous','assessment_list','group_list'));
        }else{
            return redirect()->back();
        }
    }

    public function searchAssessmentlist(Request $request)
    {
    	$value         = $request->get('value');
        $ass_id        = $request->get('ass_id');
        $course_id     = $request->get('course_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $assessments->course_id)
                 ->get();

        $result = "";
        if($value!=""){
            $group_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('assessment_list.ass_id', '=', $ass_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_list.ass_name','LIKE','%'.$value.'%')
                            ->orWhere('assessment_list.ass_word','LIKE','%'.$value.'%');
                    })
                    ->where('assessment_list.status', '=', 'Active')
                    ->groupBy('assessment_list.ass_type')
                    ->get();

            $assessment_list = DB::table('assessment_list')
                        ->select('assessment_list.*')
                        ->where('assessment_list.ass_id', '=', $ass_id)
                        ->where('assessment_list.status', '=', 'Active')
                        ->Where(function($query) use ($value) {
                            $query->orWhere('assessment_list.ass_name','LIKE','%'.$value.'%')
                                ->orWhere('assessment_list.ass_word','LIKE','%'.$value.'%');
                        })
                        ->orderBy('assessment_list.ass_type')
                        ->orderBy('assessment_list.ass_name')
                        ->orderBy('assessment_list.ass_li_id')
                        ->get();
            $i=0;
            if(count($group_list)>0) {
                foreach($group_list as $row_group){
                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<div class="col-12 row" style="padding:10px;margin: 0px;">';
                    $result .= '<div class="checkbox_group_style">';
                    $result .= '<input type="checkbox" id="group_'.$row_group->ass_type.'" class="group_checkbox">';
                    $result .= '</div>';
                    $result .= '<h5 class="group plus" id="'.$i.'">'.$row_group->ass_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_list as $row){
                        if($row_group->ass_type == $row->ass_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_li_id.'_'.$row->ass_type.'" class="group_'.$row_group->ass_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/CourseList/PastYear/images/assessment/'.$course_id."-".$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$previous[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='.$character."/CourseList/PastYear/assessment/view/whole_paper/".$course_id."-".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
                    }
                    $i++;
                    $result .= '</div></div>';
                }
            }else{
                $result .= '<div class="col-md-12" style="position:relative;top:10px;">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $group_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('ass_id', '=', $ass_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('ass_type')
                    ->get();

            $assessment_list = DB::table('assessment_list')
                        ->select('assessment_list.*')
                        ->where('ass_id', '=', $ass_id)
                        ->where('status', '=', 'Active')
                        ->orderBy('assessment_list.ass_type')
                        ->orderBy('assessment_list.ass_name')
                        ->orderBy('assessment_list.ass_li_id')
                        ->get();
            $i = 0;
            if(count($group_list)>0) {
                foreach($group_list as $row_group){
                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<div class="col-12 row" style="padding:10px;margin: 0px;">';
                    $result .= '<div class="checkbox_group_style">';
                    $result .= '<input type="checkbox" id="group_'.$row_group->ass_type.'" class="group_checkbox">';
                    $result .= '</div>';
                    $result .= '<h5 class="group plus" id="'.$i.'">'.$row_group->ass_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_list as $row){
                        if($row_group->ass_type == $row->ass_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_li_id.'_'.$row->ass_type.'" class="group_'.$row_group->ass_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/CourseList/PastYear/images/assessment/'.$course_id."-".$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$previous[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='.$character."CourseList/PastYear/assessment/view/whole_paper/".$course_id."-".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
                    }
                    $i++;
                    $result .= '</div></div>';
                }
            }else{
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">';
                $result .= '<center>Empty</center>';
                $result .= '</div>';
            }
        }
        return $result;	
   	}


    //Result
    public function searchAssessmentResult(Request $request)
    {
    	$value         = $request->get('value');
        $course_id     = $request->get('course_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $subjects = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $result = "";

        if($value!=""){
        	$data_name = DB::table('assessments')
                    ->join('courses','courses.course_id','=','assessments.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('subjects.*','courses.*','semesters.*','assessments.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessments.assessment_name','LIKE','%'.$value.'%')
                        	->orWhere('assessments.assessment','LIKE','%'.$value.'%');
                    })
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->orderBy('assessments.assessment_name')
                    ->get();
           	if(count($data_name)>0) {
           		$result .= '<input type="hidden" id="data" value="name">';
           		foreach($data_name as $ass_row_name){
           			$result .= '<div class="col-12 row align-self-center" id="course_list">';
	                $result .= '<div class="col-12 row align-self-center">';
	                $result .= '<div class="checkbox_style align-self-center">';
	                $result .= '<input type="checkbox" value="'.$ass_row_name->ass_id.'" class="group_r group_download">';
	                $result .= '</div>';
	                $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/name/'.$ass_row_name->ass_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
	                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
	                $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
	                $result .= '</div>';
	                $result .= '<div class="col-10" id="assessment_name">';
	                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$ass_row_name->semester_name." : ".$ass_row_name->assessment_name.'</b></p>';
	                $result .= '</div>';
	          		$result .= '</a>';
	                $result .= '</div>';
	                $result .= '</div>';
           		}
           	}else{
           		$batch_list = DB::table('assessment_result_students')
			                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
			                 ->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
			                 ->join('courses','assessments.course_id','=','courses.course_id')
			                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
			                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
			                 ->select('assessment_result_students.*','students.*','courses.*','semesters.*','subjects.*')
			                 ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
			                 ->where('courses.course_id','!=',$course_id)
			                 ->Where(function($query) use ($value) {
			                    $query->orWhere('students.batch','LIKE','%'.$value.'%')
			                    	->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
			                 })
			                 ->where('assessment_result_students.status','=','Active')
			                 ->groupBy('students.batch')
			                 ->groupBy('courses.semester')
			                 ->orderByDesc('semesters.semester_name')
			                 ->get();
			    if(count($batch_list)>0) {
				    $result .= '<input type="hidden" id="data" value="batch">';
				    foreach($batch_list as $rs_row){
	           			$result .= '<div class="col-12 row align-self-center" id="course_list">';
		                $result .= '<div class="col-8 row align-self-center">';
		                $result .= '<div class="checkbox_style align-self-center">';
		                $result .= '<input type="checkbox" value="'.$rs_row->ar_stu_id.'" class="group_r group_download">';
		                $result .= '</div>';
		                $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/previous/'.$rs_row->course_id.'/'.$rs_row->batch.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
		                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
		                $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
		                $result .= '</div>';
		                $result .= '<div class="col-10" id="course_name">';
		                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$rs_row->semester_name." : Assessment Lists ( ".$rs_row->batch.' )</b></p>';
		                $result .= '</div>';
		          		$result .= '</a>';
		                $result .= '</div>';
		                $result .= '</div>';
	           		}
	           	}else{
	           		$student_list = DB::table('assessment_result_students')
	           					->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
				                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
				                 ->join('users','users.user_id', '=', 'students.user_id')
				                 ->join('courses','assessments.course_id','=','courses.course_id')
				                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
				                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
				                 ->select('assessment_result_students.*','students.*','users.*','assessments.*','courses.*','semesters.*','subjects.*')
				                 ->Where(function($query) use ($value) {
				                    $query->orWhere('assessment_result_students.student_id','LIKE','%'.$value.'%')
				                        ->orWhere('students.batch','LIKE','%'.$value.'%')
				                        ->orWhere('users.name','LIKE','%'.$value.'%');
				                 })
				                 ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
				                 ->where('courses.course_id','!=',$course_id)
				                 ->where('assessment_result_students.status','=','Active')
				                 ->groupBy('assessment_result_students.student_id')
				                 ->groupBy('courses.semester')
				                 ->orderByDesc('semesters.semester_name')
				                 ->get();
				    if(count($student_list)>0) {
				    	$result .= '<input type="hidden" id="data" value="student">';
					    foreach($student_list as $stu_row){
		           			$result .= '<div class="col-12 row align-self-center" id="course_list">';
			                $result .= '<div class="col-8 row align-self-center">';
			                $result .= '<div class="checkbox_style align-self-center">';
			                $result .= '<input type="checkbox" value="'.$stu_row->ar_stu_id.'" class="group_r group_download">';
			                $result .= '</div>';
			                $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/previous/'.$stu_row->course_id.'/'.$stu_row->student_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
			                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
			                $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
			                $result .= '</div>';
			                $result .= '<div class="col-10" id="course_name">';
			                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$stu_row->semester_name." : Assessment Lists ( ".$stu_row->student_id." ".$stu_row->name.' )</b></p>';
			                $result .= '</div>';
			          		$result .= '</a>';
			                $result .= '</div>';
			                $result .= '</div>';
		           		}
	           		}else{
	           			$submitted_list = DB::table('assessment_result_students')
	           					->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
				                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
				                 ->join('users','users.user_id', '=', 'students.user_id')
				                 ->join('courses','assessments.course_id','=','courses.course_id')
				                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
				                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
				                 ->select('assessment_result_students.*','students.*','users.*','assessments.*','courses.*','semesters.*','subjects.*')
				                 ->where('courses.course_id','!=',$course_id)
				                 ->Where(function($query) use ($value) {
				                    $query->orWhere('assessment_result_students.submitted_by','LIKE','%'.$value.'%');
				                 })
				                 ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
				                 ->where('assessment_result_students.status','=','Active')
				                 ->groupBy('assessment_result_students.submitted_by')
				                 ->groupBy('courses.semester')
				                 ->orderByDesc('semesters.semester_name')
				                 ->get();
				        if(count($submitted_list)>0){
				        	$result .= '<input type="hidden" id="data" value="submitted_by">';
					    	foreach($submitted_list as $sub_row){
			           			$result .= '<div class="col-12 row align-self-center" id="course_list">';
				                $result .= '<div class="col-8 row align-self-center">';
				                $result .= '<div class="checkbox_style align-self-center">';
				                $result .= '<input type="checkbox" value="'.$sub_row->ar_stu_id.'" class="group_r group_download">';
				                $result .= '</div>';
				                $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/previous/'.$sub_row->course_id.'/'.$sub_row->submitted_by.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
				                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
				                $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
				                $result .= '</div>';
				                $result .= '<div class="col-10" id="course_name">';
				                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$sub_row->semester_name.' : Assessment Lists ( '.$sub_row->submitted_by.' )</b></p>';
				                $result .= '</div>';
				          		$result .= '</a>';
				                $result .= '</div>';
				                $result .= '</div>';
		           			}
				        }else{
				        	$result .= '<div class="col-md-12" style="position:relative;top:10px;">';
		                	$result .= '<p>Not Found</p>';
		                	$result .= '</div>';
				        }
	           		}
	           	}
           	}
        }else{
        	$previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessments','courses.course_id','=','assessments.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();
            foreach($previous_semester as $row){
           		$result .= '<div class="col-12 row align-self-center" id="course_list">';
	            $result .= '<div class="col-8 row align-self-center">';
	            $result .= '<div class="checkbox_style align-self-center">';
	            $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_r group_download">';
	            $result .= '</div>';
	            $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/previous/'.$row->course_id.'/All" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
	            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
	            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
	            $result .= '</div>';
	            $result .= '<div class="col-10" id="course_name">';
	            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->semester_name.'</b></p>';
	            $result .= '</div>';
	        	$result .= '</a>';
	        	$result .= '</div>';
	        	$result .= '<div class="col-4 row align-self-center" id="lecturer_name">';
                $result .= '<p style="width: 100%;">Lecturer : '.$row->name.'</p>';
                $result .= '</div>';
	            $result .= '</div>';
	            $result .= '</div>';
           	}
        }
        return $result;
    }

    public function PastYearResultAssessmentList($id,$course_id,$search)
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

        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $assessments = DB::table('assessments')
                 ->select('assessments.*')
                 ->where('assessments.course_id', '=', $course_id)
                 ->where('status', '=', 'Active')
                 ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.PastYear.viewSRAssessmentList',compact('search','id','course','previous','assessments'));
        }else{
            return redirect()->back();
        }
    }


    public function searchAssessmentSampleResult(Request $request)
    {
    	$value         = $request->get('value');
        $course_id     = $request->get('course_id');
        $original_id   = $request->get('original_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $result = "";

        if($value!=""){
			$batch_list = DB::table('assessment_result_students')
			        ->join('students','assessment_result_students.student_id','=','students.student_id')
			       	->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
			        ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
			        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
			        ->select('assessment_result_students.*','students.*','courses.*','assessments.*','semesters.*')
			        ->Where(function($query) use ($value) {
                        $query->orWhere('students.batch','LIKE','%'.$value.'%');
                    })
			        ->where('courses.course_id', '=', $course_id)
			        ->where('assessments.status', '=', 'Active')
			        ->groupBy('assessment_result_students.ass_id')
			        ->groupBy('students.batch')
			        ->orderBy('students.batch')
			        ->get();
			if(count($batch_list)>0){
				$result .= '<input type="hidden" id="data" value="batch">';
				foreach($batch_list as $row){
					$result .= '<div class="col-12 row align-self-center" id="course_list">';
		            $result .= '<div class="col-12 row align-self-center">';
		            $result .= '<div class="checkbox_style align-self-center">';
		            $result .= '<input type="checkbox" value="'.$row->batch.'" class="group_download">';
		            $result .= '</div>';
		            $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$original_id.'/name/'.$row->ass_id.'/'.$row->batch.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
		            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
		            $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
		            $result .= '</div>';
		            $result .= '<div class="col-10" id="assessment_name">';
		            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->assessment_name.' ( '.$row->batch.' ) </b></p>';
		            $result .= '</div>';
		        	$result .= '</a>';
		            $result .= '</div>';
		            $result .= '</div>';
	        	}
			}else{
				$student_list = DB::table('assessment_result_students')
			        ->join('students','assessment_result_students.student_id','=','students.student_id')
			        ->join('users','users.user_id','=','students.user_id')
			       	->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
			        ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
			        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
			        ->select('assessment_result_students.*','students.*','courses.*','assessments.*','semesters.*','users.*')
			        ->Where(function($query) use ($value) {
                        $query->orWhere('students.student_id','LIKE','%'.$value.'%')
                        	  ->orWhere('users.name','LIKE','%'.$value.'%');
                    })
                    ->groupBy('assessment_result_students.ass_id')
			        ->groupBy('students.batch')
			        ->where('courses.course_id', '=', $course_id)
			        ->where('assessments.status', '=', 'Active')
			        ->orderBy('students.batch')
			        ->get();
			    if(count($student_list)>0){
			    	$result .= '<input type="hidden" id="data" value="student">';
				    foreach($student_list as $row){
						$result .= '<div class="col-12 row align-self-center" id="course_list">';
			            $result .= '<div class="col-12 row align-self-center">';
			            $result .= '<div class="checkbox_style align-self-center">';
			            $result .= '<input type="checkbox" value="'.$row->student_id.'" class="group_download">';
			            $result .= '</div>';
			            $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$original_id.'/result/'.$row->ar_stu_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
			            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
			            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
			            $result .= '</div>';
			            $result .= '<div class="col-10" id="assessment_word">';
			            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->assessment_name.' ( '.$row->student_id." ".$row->name.' ) </b></p>';
			            $result .= '</div>';
			        	$result .= '</a>';
			            $result .= '</div>';
			            $result .= '</div>';
		        	}
		        }else{
		        	$submitted_list = DB::table('assessment_result_students')
			        ->join('students','assessment_result_students.student_id','=','students.student_id')
			        ->join('users','users.user_id','=','students.user_id')
			       	->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
			        ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
			        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
			        ->select('assessment_result_students.*','students.*','courses.*','assessments.*','semesters.*','users.*')
			        ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_result_students.submitted_by','LIKE','%'.$value.'%');
                    })
                    ->groupBy('assessment_result_students.ass_id')
			        ->groupBy('students.batch')
			        ->where('courses.course_id', '=', $course_id)
			        ->where('assessments.status', '=', 'Active')
			        ->orderBy('students.batch')
			        ->get();
			        if(count($submitted_list)>0){
			        	$result .= '<input type="hidden" id="data" value="submitted_by">';
					    foreach($submitted_list as $row){
							$result .= '<div class="col-12 row align-self-center" id="course_list">';
				            $result .= '<div class="col-12 row align-self-center">';
				            $result .= '<div class="checkbox_style align-self-center">';
				            $result .= '<input type="checkbox" value="'.$row->ar_stu_id.'" class="group_download">';
				            $result .= '</div>';
				            $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$original_id.'/name/'.$row->ass_id.'/'.$row->submitted_by.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
				            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
				            $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
				            $result .= '</div>';
				            $result .= '<div class="col-10" id="assessment_name">';
				            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->assessment_name.' ( '.$row->submitted_by.' ) </b></p>';
				            $result .= '</div>';
				        	$result .= '</a>';
				            $result .= '</div>';
				            $result .= '</div>';
			        	}
			        }else{
		        		$result .= '<div class="col-md-12" style="position:relative;top:10px;">';
	                	$result .= '<p>Not Found</p>';
	                	$result .= '</div>';
	            	}
		        }
			}
        }else{
        	$assessments = DB::table('assessments')
                 ->select('assessments.*')
                 ->where('assessments.course_id', '=', $course_id)
                 ->where('status', '=', 'Active')
                 ->get();

            foreach($assessments as $row){
					$result .= '<div class="col-12 row align-self-center" id="course_list">';
		            $result .= '<div class="col-12 row align-self-center">';
		            $result .= '<div class="checkbox_style align-self-center">';
		            $result .= '<input type="checkbox" value="'.$row->ass_id.'" class="group_download">';
		            $result .= '</div>';
		            $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$original_id.'/name/'.$row->ass_id.'/All" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
		            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
		            $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
		            $result .= '</div>';
		            $result .= '<div class="col-10" id="assessment_name">';
		            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->assessment_name.'</b></p>';
		            $result .= '</div>';
		        	$result .= '</a>';
		            $result .= '</div>';
		            $result .= '</div>';
	        	}
        }
        return $result;
    }

    public function PastYearStudentList($id,$ass_id,$search)
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

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $assessments->course_id;
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

   		$lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        $student_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Students')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.PastYear.viewSRStudentList',compact('search','id','course','lecturer_result','student_result','assessments','previous'));
        }else{
            return redirect()->back();
        }
    }

    public function searchStudentList(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');
        $ass_id        = $request->get('ass_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $result = "";
        if($value!=""){
            $result_list = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->Where(function($query) use ($value) {
                    $query->orWhere('assessment_result_students.student_id','LIKE','%'.$value.'%')
                    	->orWhere('assessment_result_students.submitted_by','LIKE','%'.$value.'%')
                        ->orWhere('students.batch','LIKE','%'.$value.'%')
                        ->orWhere('users.name','LIKE','%'.$value.'%');
                 })
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

            $check_submitted_list = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->Where(function($query) use ($value) {
                    $query->orWhere('assessment_result_students.submitted_by','LIKE','%'.$value.'%');
                 })
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

            $result .= '<div class="col-12 row" style="padding: 0px 10px;margin: 0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" id="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<div class="l_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">';
            $result .= '<div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">';
            $result .= '    Searched Result (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 7px;"></i>)';
            $result .= '</div>';
            $result .= '<div class="col-9 show_count" style="border:0px solid black;">';
            $result .= '    <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">';
            $result .= '    <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( '.count($result_list).' ) </span>';
            $result .= '</div></div></div>';
            if(count($result_list)>0) {
            	$result .= '<div class="row col-md-12" id="lecturer"  style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">';
                foreach($result_list as $row){
                    $result .= '<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    if(count($check_submitted_list)>0){
                    	$result .= '<input type="checkbox" value="'.$row->student_id.'_'.$row->submitted_by.'" class="group_lecturer group_download">';
                    }else{
                    	$result .= '<input type="checkbox" value="'.$row->student_id.'_All" class="group_lecturer group_download">';
                    }
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/result/'.$row->ar_stu_id.'" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
                    $result .= '<div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">';
                    $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>'.$row->student_id.' ( '.$row->name.' ) </b></p>';
                    $result .= '</div></div></a></div>';
                }
            }else{
                $result .= '<div class="col-md-12" id="lecturer" style="border:0px solid black;padding-top:10px;padding-left:25px;">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

            $student_result = DB::table('assessment_result_students')
                     ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                     ->join('users','users.user_id', '=', 'students.user_id')
                     ->select('assessment_result_students.*','students.*','users.*')
                     ->where('assessment_result_students.ass_id', '=', $ass_id)
                     ->where('assessment_result_students.submitted_by','=', 'Students')
                     ->where('assessment_result_students.status','=','Active')
                     ->groupBy('assessment_result_students.student_id')
                     ->get();

            if(count($lecturer_result)>0) {
            $result .= '<div class="col-12 row" style="padding: 0px 10px;margin: 0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" id="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<div class="l_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">';
            $result .= '<div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">';
            $result .= 'Submitted By Lecturer (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 7px;"></i>)';
            $result .= '</div>';
            $result .= '<div class="col-9 show_count" style="border:0px solid black;">';
            $result .= '    <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">';
            $result .= '    <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( '.count($lecturer_result).' ) </span>';
            $result .= '</div></div></div>';
            
            $result .= '<div class="row col-md-12" id="lecturer"  style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">';
                foreach($lecturer_result as $row){
                    $result .= '<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->student_id.'_Lecturer" class="group_lecturer group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/result/'.$row->ar_stu_id.'" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
                    $result .= '<div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">';
                    $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>'.$row->student_id.' ( '.$row->name.' ) </b></p>';
                    $result .= '</div></div></a></div>';
                }
            }
            $result .= '</div>';
            
            if(count($student_result)>0) {
                $result .= '<div class="col-12 row" style="padding: 0px 10px;margin: 15px 0px 0px 0px;">';
                $result .= '<div class="checkbox_group_style align-self-center">';
                $result .= '<input type="checkbox" name="group_student" id="group_student" class="group_checkbox">';
                $result .= '</div>';
                $result .= '<div class="s_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">';
                $result .= '<div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">';
                $result .= 'Submitted By Students (<i class="fa fa-minus" aria-hidden="true" id="icon_s" style="color: #0d2f81;position: relative;top: 7px;"></i>)';
                $result .= '</div>';
                $result .= '<div class="col-9 show_count" style="border:0px solid black;">';
                $result .= '    <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">';
                $result .= '    <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( '.count($student_result).' ) </span>';
                $result .= '</div></div></div>';

                $result .= '<div class="row col-md-12" id="student"  style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">';
            
                foreach($student_result as $sow){
                    $result .= '<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$sow->student_id.'_Students" class="group_student group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/CourseList/PastYear/sampleResult/'.$course_id.'/result/'.$sow->ar_stu_id.'" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
                    $result .= '<div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">';
                    $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>'.$sow->student_id.' ( '.$sow->name.' ) </b></p>';
                    $result .= '</div></div></a></div>';
                }
            }

            if((count($lecturer_result)==0)&&(count($student_result)==0)){
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin:5px 20px;">
                                <center>Empty</center></div>';
            }
        }
        return $result;
    }

    public function PastYearResultList($id,$ar_stu_id)
    {
    	$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $assessment_result_student = AssessmentResultStudent::where('ar_stu_id', '=', $ar_stu_id)->firstOrFail();
        $ass_id = $assessment_result_student->ass_id;
        $student_id = $assessment_result_student->student_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $assessments->course_id;

        if(auth()->user()->position=="Dean"){
            $check_course = DB::table('courses')
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
            $check_course = DB::table('courses')
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

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.course_id', '=', $course_id)
                 ->get();
        $lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->where('assessment_result_students.student_id','=',$student_id)
                 ->orderBy('assessment_result_students.document_name')
                 ->get();

        $student_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Students')
                 ->where('assessment_result_students.status','=','Active')
                 ->where('assessment_result_students.student_id','=',$student_id)
                 ->orderBy('assessment_result_students.document_name')
                 ->get();

        if(count($check_course)>0){
            return view('dean.CoursePortFolio.PastYear.viewSRResultList',compact('id','course','assessments','assessment_result_student','lecturer_result','student_result'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadFiles($ass_li_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $string = explode('-',$ass_li_id);

        $assessment_list = AssessmentList::where('ass_li_id', '=', $string[1])->firstOrFail();
        $ass_id = $assessment_list->ass_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $question = $assessments->assessment_name;

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
                     ->where('courses.course_id', '=', $string[0])
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
                     ->where('courses.course_id', '=', $string[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        if(count($course)>0){
            $ext = "";
            if($assessment_list->ass_document!=""){
                $ext = explode(".", $assessment_list->ass_document);
            }
            return Storage::disk('private')->download('Assessment/'.$assessment_list->ass_document, $question."_".$assessment_list->ass_type."_".$assessment_list->ass_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function assessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

       	$string = explode('-',$image_name);

        $checkImageASSID = AssessmentList::where('ass_document', '=', $string[1])->firstOrFail();
        $ass_id = $checkImageASSID->ass_id;

        $checkCourseId = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

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
                     ->where('courses.course_id', '=', $string[0])
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
                     ->where('courses.course_id', '=', $string[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment/' . $string[1]);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function view_wholePaper($ass_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $string = explode('-',$ass_id);
        $assessments = Assessments::where('ass_id', '=', $string[1])->firstOrFail();

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
                     ->where('courses.course_id', '=', $string[0])
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
                     ->where('courses.course_id', '=', $string[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        $question = $assessments->assessment;

        $assessment_list = DB::table('assessment_list')
                    ->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessment_list.*','courses.*','semesters.*')
                    ->where('assessment_list.ass_id', '=', $string[1])
                    ->where('assessment_list.status', '=', 'Active')
                    ->orderBy('assessment_list.ass_id')
                    ->orderBy('assessment_list.ass_type')
                    ->orderBy('assessment_list.ass_name')
                    ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.Assessment.viewWholePaper', compact('assessments','assessment_list','question','string'));
        }else{
            return redirect()->back();
        }
    }


    public function downloadDocument($ar_stu_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $string = explode('-',$ar_stu_id);

        $assessment_result_student = AssessmentResultStudent::where('ar_stu_id', '=', $string[1])->firstOrFail();
        $ass_id = $assessment_result_student->ass_id;
        $student_id = $assessment_result_student->student_id;

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
                     ->where('courses.course_id', '=', $string[0])
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
                     ->where('courses.course_id', '=', $string[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        if(count($course)>0){
            $ext = "";
            if($assessment_result_student->document!=""){
                $ext = explode(".", $assessment_result_student->document);
            }
            return Storage::disk('private')->download('Assessment_Result/'.$assessment_result_student->document, $assessment_result_student->document_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function assessmentResult_image($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $string = explode('-',$image_name);

        $checkRSID = AssessmentResultStudent::where('document', '=', $string[1])->firstOrFail();
        $ass_id = $checkRSID->ass_id;

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
                     ->where('courses.course_id', '=', $string[0])
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
                     ->where('courses.course_id', '=', $string[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment_Result/' . $string[1]);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }


    public function view_wholePaperResult($ar_stu_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $string = explode('-',$ar_stu_id);

        $checkARID = AssessmentResultStudent::where('ar_stu_id', '=', $string[1])->firstOrFail();
        $ass_id = $checkARID->ass_id;
        $submitted_by = $checkARID->submitted_by;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

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
                     ->where('courses.course_id', '=', $string[0])
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
                     ->where('courses.course_id', '=', $string[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        $assessment_result_list = DB::table('assessment_result_students')
                                ->select('assessment_result_students.*')
                                ->where('assessment_result_students.ass_id','=',$ass_id)
                                ->where('assessment_result_students.submitted_by','=',$checkARID->submitted_by)
                                ->where('assessment_result_students.student_id','=',$checkARID->student_id)
                                ->get();
        if(count($course)>0){
            return view('dean.CoursePortFolio.AssessmentResult.viewWholePaper', compact('assessment_result_list','assessments','checkARID','submitted_by','string'));
        }else{
            return redirect()->back();
        }
    }
}
