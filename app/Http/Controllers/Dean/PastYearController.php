<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Assessments;
use App\AssessmentList;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class PastYearController extends Controller
{
    public function PastYearAssessment($id)
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
            return view('dean.PastYear.viewAssessment',compact('course','previous_semester'));
        }else{
            return redirect()->back();
        }
    }

    public function zipFileDownload($course_id,$download)
    {
    	if($download == "All"){
    		$f_course_id = $course_id;
        }else{
            $string = explode('---',$course_id);
            $f_course_id = $string[0];
        }

        $subjects = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $f_course_id)
                 ->get();

        $name = $subjects[0]->subject_name." ( ".$subjects[0]->subject_code." )";

        if($download == "checked"){
        	$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment/PastYear/'.$name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment/'));
            for($i=1;$i<(count($string)-1);$i++){

            	$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $string[$i])
                 ->get();

                $zip->addEmptyDir($course[0]->semester_name);

            	$assessments = DB::table('assessments')
		                 ->select('assessments.*')
		                 ->where('course_id', '=', $string[$i])
		                 ->where('status', '=', 'Active')
		                 ->get();
                foreach($assessments as $ass_row){
                	$zip->addEmptyDir($course[0]->semester_name."/".$ass_row->assessment_name);
                	$result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $ass_row->ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();
                    foreach($result_list as $rl_row){
		                if($rl_row->ass_type=="Question"){
		                    $zip->addEmptyDir($course[0]->semester_name."/".$ass_row->assessment_name."/Question");
		                }else{
		                    $zip->addEmptyDir($course[0]->semester_name."/".$ass_row->assessment_name."/Solution"); 
		                }
		                foreach ($files as $key => $value) {
		                    $relativeNameInZipFile = basename($value);
		                    if($rl_row->ass_document == $relativeNameInZipFile){
		                        $ext = explode('.',$relativeNameInZipFile);
		                        if($rl_row->ass_type=="Question"){
		                            $zip->addFile($value,$course[0]->semester_name."/".$ass_row->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
		                        }else{
		                            $zip->addFile($value,$course[0]->semester_name."/".$ass_row->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
		                        } 
		                    }
		                }
		            }
                }   
		   	}
		}else if($download == "searched"){
			$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment/PastYear/'.$name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment/'));
			for($i=1;$i<(count($string)-1);$i++){
				$assessments = Assessments::where('ass_id','=',$string[$i])->firstOrFail();
				$course_id = $assessments->course_id;

				$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

                 $zip->addEmptyDir($course[0]->semester_name."/".$assessments->assessment_name);

                 $result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $assessments->ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();

                 foreach($result_list as $rl_row){
		            if($rl_row->ass_type=="Question"){
		                $zip->addEmptyDir($course[0]->semester_name."/".$assessments->assessment_name."/Question");
		            }else{
		                $zip->addEmptyDir($course[0]->semester_name."/".$assessments->assessment_name."/Solution"); 
		            }
		            foreach ($files as $key => $value) {
		                $relativeNameInZipFile = basename($value);
		                if($rl_row->ass_document == $relativeNameInZipFile){
		                    $ext = explode('.',$relativeNameInZipFile);
		                    if($rl_row->ass_type=="Question"){
		                        $zip->addFile($value,$course[0]->semester_name."/".$assessments->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
		                    }else{
		                        $zip->addFile($value,$course[0]->semester_name."/".$assessments->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
		                    } 
		                }
		            }
		        }
			}
		}else if($download == "searchedWord"){
			$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment/PastYear/'.$name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment/'));
			for($i=1;$i<(count($string)-1);$i++){

				$assessment_list = AssessmentList::where('ass_li_id','=',$string[$i])->firstOrFail();
				$ass_id = $assessment_list->ass_id;

				$assessments = Assessments::where('ass_id','=',$ass_id)->firstOrFail();
				$course_id = $assessments->course_id;

				$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

                 $zip->addEmptyDir($course[0]->semester_name."/".$assessments->assessment_name);

		         if($assessment_list->ass_type=="Question"){
		            $zip->addEmptyDir($course[0]->semester_name."/".$assessments->assessment_name."/Question");
		         }else{
		            $zip->addEmptyDir($course[0]->semester_name."/".$assessments->assessment_name."/Solution"); 
		         }
		         foreach ($files as $key => $value) {
		           	$relativeNameInZipFile = basename($value);
		            if($assessment_list->ass_document == $relativeNameInZipFile){
		                $ext = explode('.',$relativeNameInZipFile);
		                if($assessment_list->ass_type=="Question"){
		                    $zip->addFile($value,$course[0]->semester_name."/".$assessments->assessment_name."/Question/".$assessment_list->ass_name.'.'.$ext[1]);
		                }else{
		                    $zip->addFile($value,$course[0]->semester_name."/".$assessments->assessment_name."/Solution/".$assessment_list->ass_name.'.'.$ext[1]);
		                } 
		            }
		        }
			}
		}else{
			$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment/PastYear/'.$name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment/'));
	        $Resultfiles = File::files(storage_path('/private/Assessment_Result/'));

			$previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessments','courses.course_id','=','assessments.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$f_course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

            foreach($previous_semester as $p_row){
	            $zip->addEmptyDir("Question & Solution/".$p_row->semester_name);

	           	$assessments = DB::table('assessments')
			                 ->select('assessments.*')
			                 ->where('course_id', '=', $p_row->course_id)
			                 ->where('status', '=', 'Active')
			                 ->get();

	            foreach($assessments as $ass_row){
	                	$zip->addEmptyDir("Question & Solution/".$p_row->semester_name."/".$ass_row->assessment_name);
	                	$result_list = DB::table('assessment_list')
	                         ->select('assessment_list.*')
	                         ->where('assessment_list.ass_id', '=', $ass_row->ass_id)
	                         ->where('assessment_list.status','=','Active')
	                         ->get();
	                foreach($result_list as $rl_row){
			                if($rl_row->ass_type=="Question"){
			                    $zip->addEmptyDir("Question & Solution/".$p_row->semester_name."/".$ass_row->assessment_name."/Question");
			                }else{
			                    $zip->addEmptyDir("Question & Solution/".$p_row->semester_name."/".$ass_row->assessment_name."/Solution"); 
			                }
			            foreach ($files as $key => $value) {
			                $relativeNameInZipFile = basename($value);
			                if($rl_row->ass_document == $relativeNameInZipFile){
			                    $ext = explode('.',$relativeNameInZipFile);
			                    if($rl_row->ass_type=="Question"){
			                        $zip->addFile($value,"Question & Solution/".$p_row->semester_name."/".$ass_row->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
			                    }else{
			                        $zip->addFile($value,"Question & Solution/".$p_row->semester_name."/".$ass_row->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
			                    } 
			                }
			            }
			        }
	            }
        	}

        	foreach($previous_semester as $p_row){
        		$zip->addEmptyDir("Sample Result/".$p_row->semester_name);

        		$assessments = DB::table('assessments')
			                 ->select('assessments.*')
			                 ->where('course_id', '=', $p_row->course_id)
			                 ->where('status', '=', 'Active')
			                 ->get();

			    foreach($assessments as $ass_row){
	                $zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->assessment_name);

	                $group_result = DB::table('assessment_result_students')
                         ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                         ->join('users','users.user_id', '=', 'students.user_id')
                         ->select('assessment_result_students.*','students.*','users.*')
                         ->where('assessment_result_students.ass_id', '=', $ass_row->ass_id)
                         ->where('assessment_result_students.status','=','Active')
                         ->groupBy('assessment_result_students.student_id')
                         ->get();
                    foreach($group_result as $row){
                    	$zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->assessment_name."/".$row->student_id);

                    	$result_list = DB::table('assessment_result_students')
                                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                                 ->join('users','users.user_id', '=', 'students.user_id')
                                 ->select('assessment_result_students.*','students.*','users.*')
                                 ->where('assessment_result_students.ass_id', '=', $ass_row->ass_id)
                                 ->where('assessment_result_students.status','=','Active')
                                 ->where('assessment_result_students.student_id','=',$row->student_id)
                                 ->get();

                        foreach($result_list as $rl_row){
	                        if($rl_row->submitted_by=="Lecturer"){
	                            $zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->assessment_name."/".$row->student_id."/Lecturer");
	                        }else{
	                            $zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->assessment_name."/".$row->student_id."/Students"); 
	                        }
	                        foreach ($Resultfiles as $key => $value) {
	                            $relativeNameInZipFile = basename($value);
	                            if($rl_row->document == $relativeNameInZipFile){
	                                $ext = explode('.',$relativeNameInZipFile);
	                                if($rl_row->submitted_by=="Lecturer"){
	                                    $zip->addFile($value,"Sample Result/".$p_row->semester_name."/".$ass_row->assessment_name."/".$row->student_id."/Lecturer/".$rl_row->document_name.'.'.$ext[1]);
	                                }else{
	                                    $zip->addFile($value,"Sample Result/".$p_row->semester_name."/".$ass_row->assessment_name."/".$row->student_id."/Students/".$rl_row->document_name.'.'.$ext[1]);
	                                } 
	                            }
	                        }
	                    }
                    }
	            }
        	}
		}
		$zip->close();
    	return response()->download($fileName);
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
	                $result .= '<a href="/PastYear/assessment/'.$course_id.'/list/'.$ass_row_name->ass_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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
                        $result .= '<a href="/images/assessment/'.$ass_row_word->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$ass_row_word->semester_name.' : '.$ass_row_word->assessment_name.' / '.$ass_row_word->ass_type.' / '.$ass_row_word->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$ass_row_word->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
                	$result .= '<div class="col-md-12" style="position:relative;top:10px;">';
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
	            $result .= '<a href="/PastYear/assessment/'.$course_id.'/assessment_name/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

       	$previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

        $assessments = DB::table('assessments')
                 ->select('assessments.*')
                 ->where('assessments.course_id', '=', $course_id)
                 ->where('status', '=', 'Active')
                 ->get();

        if(count($course)>0){
            return view('dean.PastYear.viewAssessmentName',compact('course','previous','assessments','id'));
        }else{
            return redirect()->back();
        }
    }

	public function zipFileDownloadName($course_id,$download)
    {
    	if($download == "All"){
    		$f_course_id = $course_id;
        }else{
            $string = explode('---',$course_id);
            $f_course_id = $string[0];
        }

        $subjects = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $f_course_id)
                 ->get();

        $name = $subjects[0]->subject_code." ".$subjects[0]->subject_name." ( ".$subjects[0]->semester_name." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment/PastYear/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment/'));

        if($download == "checked"){
           	for($i=1;$i<(count($string)-1);$i++){

           		$assessments = Assessments::where('ass_id','=',$string[$i])->firstOrFail();
				$course_id = $assessments->course_id;

                $zip->addEmptyDir($assessments->assessment_name);
                $result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $assessments->ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();
                foreach($result_list as $rl_row){
		            if($rl_row->ass_type=="Question"){
		                $zip->addEmptyDir($assessments->assessment_name."/Question");
		            }else{
		                $zip->addEmptyDir($assessments->assessment_name."/Solution"); 
		            }
		            foreach ($files as $key => $value) {
		                $relativeNameInZipFile = basename($value);
		                if($rl_row->ass_document == $relativeNameInZipFile){
		                    $ext = explode('.',$relativeNameInZipFile);
		                    if($rl_row->ass_type=="Question"){
		                        $zip->addFile($value,$assessments->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
		                    }else{
		                        $zip->addFile($value,$assessments->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
		                    } 
		                }
		            }
		        }
		   	}
		}else if($download == "searchedWord"){
			for($i=1;$i<(count($string)-1);$i++){

				$assessment_list = AssessmentList::where('ass_li_id','=',$string[$i])->firstOrFail();
				$ass_id = $assessment_list->ass_id;

				$assessments = Assessments::where('ass_id','=',$ass_id)->firstOrFail();
				$course_id = $assessments->course_id;

				$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

                 $zip->addEmptyDir($assessments->assessment_name);

		         if($assessment_list->ass_type=="Question"){
		            $zip->addEmptyDir($assessments->assessment_name."/Question");
		         }else{
		            $zip->addEmptyDir($assessments->assessment_name."/Solution"); 
		         }
		         foreach ($files as $key => $value) {
		           	$relativeNameInZipFile = basename($value);
		            if($assessment_list->ass_document == $relativeNameInZipFile){
		                $ext = explode('.',$relativeNameInZipFile);
		                if($assessment_list->ass_type=="Question"){
		                    $zip->addFile($value,$assessments->assessment_name."/Question/".$assessment_list->ass_name.'.'.$ext[1]);
		                }else{
		                    $zip->addFile($value,$assessments->assessment_name."/Solution/".$assessment_list->ass_name.'.'.$ext[1]);
		                } 
		            }
		        }
			}
		}else{
	        $assessments = DB::table('assessments')
			                 ->select('assessments.*')
			                 ->where('course_id', '=', $course_id)
			                 ->where('status', '=', 'Active')
			                 ->get();

	        foreach($assessments as $ass_row){
	                	$zip->addEmptyDir($ass_row->assessment_name);
	                	$result_list = DB::table('assessment_list')
	                         ->select('assessment_list.*')
	                         ->where('assessment_list.ass_id', '=', $ass_row->ass_id)
	                         ->where('assessment_list.status','=','Active')
	                         ->get();
	            foreach($result_list as $rl_row){
			                if($rl_row->ass_type=="Question"){
			                    $zip->addEmptyDir($ass_row->assessment_name."/Question");
			                }else{
			                    $zip->addEmptyDir($ass_row->assessment_name."/Solution"); 
			                }
			        foreach ($files as $key => $value) {
			            $relativeNameInZipFile = basename($value);
			            if($rl_row->ass_document == $relativeNameInZipFile){
			                $ext = explode('.',$relativeNameInZipFile);
			                if($rl_row->ass_type=="Question"){
			                    $zip->addFile($value,$ass_row->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
			                }else{
			                    $zip->addFile($value,$ass_row->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
			                } 
			            }
			        }
			    }
	        }
		}
		$zip->close();
    	return response()->download($fileName);
    }

    public function searchAssessmentName(Request $request)
    {
    	$value         = $request->get('value');
        $course_id     = $request->get('course_id');

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
                    $result .= '<a href="/images/assessment/'.$ass_row_word->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$ass_row_word->semester_name.' : '.$ass_row_word->assessment_name.' / '.$ass_row_word->ass_type.' / '.$ass_row_word->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$ass_row_word->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
	            $result .= '<a href="" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

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
            return view('dean.PastYear.viewAssessmentList',compact('course','assessments','previous','assessment_list','group_list'));
        }else{
            return redirect()->back();
        }
    }

    public function zipFileDownloadList($ass_id,$download)
    {
    	 if($download == "checked"){
            $string = explode('---',$ass_id);
            $f_ass_id = $string[0];
        }else{
            $f_ass_id = $ass_id;
        }

        $assessments = Assessments::where('ass_id', '=', $f_ass_id)->firstOrFail();
        $course_id = $assessments->course_id;

        $subjects = DB::table('subjects')
                        ->join('courses','courses.subject_id','=','subjects.subject_id')
                        ->join('semesters','courses.semester','=','semesters.semester_id')
                        ->select('courses.*','subjects.*','semesters.*')
                        ->where('courses.course_id', '=', $course_id)
                        ->get();

        $name = $subjects[0]->semester_name." ".$assessments->assessment_name." ( ".$subjects[0]->subject_code." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment/'));
        
        if($download == "checked"){
            for($i=1;$i<(count($string)-1);$i++){
                $studentId_By = explode('_',$string[$i]);
                $zip->addEmptyDir($studentId_By[1]);
                $assessment_list = AssessmentList::where('ass_li_id', '=', $studentId_By[0])->firstOrFail();
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($assessment_list->ass_document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        $zip->addFile($value,$studentId_By[1]."/".$assessment_list->ass_name.'.'.$ext[1]);
                    }
                }
            }
        }else{
            $result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();

            foreach($result_list as $rl_row){
                if($rl_row->ass_type=="Question"){
                    $zip->addEmptyDir("Question");
                }else{
                    $zip->addEmptyDir("Solution"); 
                }
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($rl_row->ass_document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($rl_row->ass_type=="Question"){
                            $zip->addFile($value,"Question/".$rl_row->ass_name.'.'.$ext[1]);
                        }else{
                            $zip->addFile($value,"Solution/".$rl_row->ass_name.'.'.$ext[1]);
                        } 
                    }
                }
            } 
        }
        $zip->close();
        return response()->download($fileName);
    }

    public function searchAssessmentlist(Request $request)
    {
    	$value         = $request->get('value');
        $ass_id        = $request->get('ass_id');

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
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
                            $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
                            $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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

   	public function downloadFiles($ass_li_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment_list = AssessmentList::where('ass_li_id', '=', $ass_li_id)->firstOrFail();

        $ass_id = $assessment_list->ass_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $question = $assessments->assessment_name;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('course_id', '=', $assessments->course_id)
                 ->get();

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


    //Result
    public function searchAssessmentResult(Request $request)
    {
    	$value         = $request->get('value');
        $course_id     = $request->get('course_id');

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
                    ->get();
           	if(count($data_name)>0) {
           		$result .= '<input type="hidden" id="data" value="name">';
           		foreach($data_name as $ass_row_name){
           			$result .= '<div class="col-12 row align-self-center" id="course_list">';
	                $result .= '<div class="col-12 row align-self-center">';
	                $result .= '<div class="checkbox_style align-self-center">';
	                $result .= '<input type="checkbox" value="'.$ass_row_name->ass_id.'" class="group_r group_download">';
	                $result .= '</div>';
	                $result .= '<a href="" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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
			                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
			                 ->select('assessment_result_students.*','students.*','courses.*','semesters.*')
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
		                $result .= '<input type="checkbox" value="'.$rs_row->batch.'" class="group_r group_download">';
		                $result .= '</div>';
		                $result .= '<a href="" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
		                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
		                $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
		                $result .= '</div>';
		                $result .= '<div class="col-10" id="course_name">';
		                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$rs_row->semester_name." : ".$rs_row->batch.'</b></p>';
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
				                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
				                 ->select('assessment_result_students.*','students.*','users.*','assessments.*','courses.*','semesters.*')
				                 ->where('courses.course_id','!=',$course_id)
				                 ->Where(function($query) use ($value) {
				                    $query->orWhere('assessment_result_students.student_id','LIKE','%'.$value.'%')
				                        ->orWhere('students.batch','LIKE','%'.$value.'%')
				                        ->orWhere('users.name','LIKE','%'.$value.'%');
				                 })
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
			                $result .= '<input type="checkbox" value="'.$stu_row->student_id.'" class="group_r group_download">';
			                $result .= '</div>';
			                $result .= '<a href="" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
			                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
			                $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
			                $result .= '</div>';
			                $result .= '<div class="col-10" id="course_name">';
			                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$stu_row->semester_name." : ".$stu_row->student_id." ( ".$stu_row->name.' ) </b></p>';
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
	            $result .= '<a href="" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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
}
