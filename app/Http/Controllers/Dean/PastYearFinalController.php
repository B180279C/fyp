<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Student;
use App\Staff;
use App\AssFinal;
use App\AssessmentFinal;
use App\AssessmentFinalResult;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class PastYearFinalController extends Controller
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
                    ->join('ass_final','courses.course_id','=','ass_final.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

        $previous_result_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessment_final_result','courses.course_id','=','assessment_final_result.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

        if(count($course)>0){
            return view('dean.PastYear.viewAssessmentFinal',compact('course','previous_semester','previous_result_semester'));
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

        $ZipFile_name = $subjects[0]->subject_name." ( ".$subjects[0]->subject_code." )";

        if($download == "checked"){
        	$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment_Final/'));
            for($i=1;$i<(count($string)-1);$i++){

            	$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $string[$i])
                 ->get();

                $zip->addEmptyDir($course[0]->semester_name);

                $result_list = DB::table('assessment_final')
                         ->select('assessment_final.*')
                         ->where('assessment_final.course_id', '=', $string[$i])
                         ->where('assessment_final.status','=','Active')
                         ->get();
                foreach($result_list as $rl_row){
		            if($rl_row->ass_fx_type=="Question"){
		                $zip->addEmptyDir($course[0]->semester_name."/Question");
		            }else{
		                $zip->addEmptyDir($course[0]->semester_name."/Solution"); 
		            }
		            foreach ($files as $key => $value) {
		                $relativeNameInZipFile = basename($value);
		                if($rl_row->ass_fx_document == $relativeNameInZipFile){
		                    $ext = explode('.',$relativeNameInZipFile);
		                    if($rl_row->ass_fx_type=="Question"){
		                        $zip->addFile($value,$course[0]->semester_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
		                    }else{
		                        $zip->addFile($value,$course[0]->semester_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
		                    } 
		                }
		            }
		        }
		   	}
		}else if($download == "searchedWord"){
			$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment_Final/'));
			for($i=1;$i<(count($string)-1);$i++){

				$AssessmentFinal = AssessmentFinal::where('ass_fx_id','=',$string[$i])->firstOrFail();
				$course_id = $AssessmentFinal->course_id;

				$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

                 $zip->addEmptyDir($course[0]->semester_name);

		         if($AssessmentFinal->ass_fx_type=="Question"){
		            $zip->addEmptyDir($course[0]->semester_name."/Question");
		         }else{
		            $zip->addEmptyDir($course[0]->semester_name."/Solution"); 
		         }
		         foreach ($files as $key => $value) {
		           	$relativeNameInZipFile = basename($value);
		            if($AssessmentFinal->ass_fx_document == $relativeNameInZipFile){
		                $ext = explode('.',$relativeNameInZipFile);
		                if($AssessmentFinal->ass_fx_type=="Question"){
		                    $zip->addFile($value,$course[0]->semester_name."/Question/".$AssessmentFinal->ass_fx_name.'.'.$ext[1]);
		                }else{
		                    $zip->addFile($value,$course[0]->semester_name."/Solution/".$AssessmentFinal->ass_fx_name.'.'.$ext[1]);
		                } 
		            }
		        }
			}
		}else{
			$zip = new ZipArchive;
	        $fileName = storage_path('private/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
	        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	        $files = File::files(storage_path('/private/Assessment_Final/'));
	        $Resultfiles = File::files(storage_path('/private/Final_Result/'));

			$previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('ass_final','courses.course_id','=','ass_final.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$f_course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

            $previous_result_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessment_final_result','courses.course_id','=','assessment_final_result.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$f_course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

            foreach($previous_semester as $p_row){
	            $zip->addEmptyDir("Question & Solution/".$p_row->semester_name);

	            $result_list = DB::table('assessment_final')
	                         ->select('assessment_final.*')
	                         ->where('assessment_final.course_id', '=', $p_row->course_id)
	                         ->where('assessment_final.status','=','Active')
	                         ->get();

	            foreach($result_list as $rl_row){
			        if($rl_row->ass_fx_type=="Question"){
			            $zip->addEmptyDir("Question & Solution/".$p_row->semester_name."/Question");
			        }else{
			            $zip->addEmptyDir("Question & Solution/".$p_row->semester_name."/Solution"); 
			        }
			        foreach ($files as $key => $value) {
			            $relativeNameInZipFile = basename($value);
			            if($rl_row->ass_fx_document == $relativeNameInZipFile){
			                $ext = explode('.',$relativeNameInZipFile);
			                if($rl_row->ass_fx_type=="Question"){
			                    $zip->addFile($value,"Question & Solution/".$p_row->semester_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
			                }else{
			                    $zip->addFile($value,"Question & Solution/".$p_row->semester_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
			                } 
			            }
			        }
			    }
        	}

        	foreach($previous_result_semester as $p_row){
        		$zip->addEmptyDir("Sample Result/".$p_row->semester_name);

        		$assessments = DB::table('assessment_final_result')
			                 ->select('assessment_final_result.*')
			                 ->where('course_id', '=', $p_row->course_id)
			                 ->where('status', '=', 'Active')
			                 ->groupBy('assessment_final_result.student_id')
			                 ->get();

			    foreach($assessments as $ass_row){
	                $zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->student_id);

                    $result_list = DB::table('assessment_final_result')
                                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                                 ->join('users','users.user_id', '=', 'students.user_id')
                                 ->select('assessment_final_result.*','students.*','users.*')
                                 ->where('assessment_final_result.course_id', '=', $p_row->course_id)
                                 ->where('assessment_final_result.status','=','Active')
                                 ->where('assessment_final_result.student_id','=',$ass_row->student_id)
                                 ->get();

                    foreach($result_list as $rl_row){
	                        if($rl_row->submitted_by=="Lecturer"){
	                            $zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->student_id."/Lecturer");
	                        }else{
	                            $zip->addEmptyDir("Sample Result/".$p_row->semester_name."/".$ass_row->student_id."/Students"); 
	                        }
	                    foreach ($Resultfiles as $key => $value) {
	                        $relativeNameInZipFile = basename($value);
	                        if($rl_row->document == $relativeNameInZipFile){
	                            $ext = explode('.',$relativeNameInZipFile);
	                              	if($rl_row->submitted_by=="Lecturer"){
	                                    $zip->addFile($value,"Sample Result/".$p_row->semester_name."/".$ass_row->student_id."/Lecturer/".$rl_row->document_name.'.'.$ext[1]);    
		                            }else{
		                                $zip->addFile($value,"Sample Result/".$p_row->semester_name."/".$ass_row->student_id."/Students/".$rl_row->document_name.'.'.$ext[1]);
		                            } 
	                        }
	                    }
	                }
	            }
        	}
		}
		$zip->close();
    	if($this->checkCoursePerson($f_course_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
            return redirect()->back();
        }
    }

    public function searchAssessment(Request $request)
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
           	$data_word = DB::table('assessment_final')
                    ->join('courses','courses.course_id','=','assessment_final.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('subjects.*','courses.*','semesters.*','assessment_final.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_final.ass_fx_word','LIKE','%'.$value.'%');
                    })
                    ->where('courses.status', '=', 'Active')
                    ->where('assessment_final.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->get();

            if(count($data_word)>0) {
                	$result .= '<input type="hidden" id="data" value="word">';
                	foreach($data_word as $ass_row_word){
                		$result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-9 row align-self-center" >';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$ass_row_word->ass_fx_id.'" class="group_q group_download">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/PastYear/images/final_assessment/'.$course_id.'-'.$ass_row_word->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$ass_row_word->semester_name.' : Final / '.$ass_row_word->ass_fx_type.' / '.$ass_row_word->ass_fx_name.' <br> <a href='.$character."/PastYear/final_assessment/view/whole_paper/".$course_id."-".$ass_row_word->course_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                        $result .= '</div>';
                       	$result .= '<div class="col-10" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$ass_row_word->semester_name." : Final / ".$ass_row_word->ass_fx_type." / ".$ass_row_word->ass_fx_name.'</b></p>';
                        $result .= '</div>';
                       	$result .= '</a>';
                        $result .= '</div>';
                        $result .= '<div class="col-3" id="course_action_two">';
	                    $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$ass_row_word->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
	                    $result .= '</div>';
                        $result .= '</div>';
                }
            }else{
                $result .= '<div class="col-md-12" style="position:relative;top:5px;left:5px">';
	            $result .= '<p>Not Found</p>';
	            $result .= '</div>';
            }    
        }else{
        	$previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('ass_final','courses.course_id','=','ass_final.course_id')
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
	            $result .= '<a href="'.$character.'/PastYear/FinalAssessment/'.$course_id.'/list/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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

    // public function PastYearAssessmentName($id,$course_id)
    // {
    // 	$user_id       = auth()->user()->user_id;
    //     $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    //     $faculty_id    = $staff_dean->faculty_id;

    //     $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('lecturer', '=', $staff_dean->id)
    //              ->where('course_id', '=', $id)
    //              ->get();

    //    	$previous = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('course_id', '=', $course_id)
    //              ->get();

    //     $ass_final = DB::table('ass_final')
    //              ->select('ass_final.*')
    //              ->where('ass_final.course_id', '=', $course_id)
    //              ->where('status', '=', 'Active')
    //              ->get();

    //     if(count($course)>0){
    //         return view('dean.PastYear.viewFinalAssessmentName',compact('course','previous','ass_final','id'));
    //     }else{
    //         return redirect()->back();
    //     }
    // }

    // public function searchAssessmentName(Request $request)
    // {
    // 	$value         = $request->get('value');
    //     $course_id     = $request->get('course_id');
    //     $original_id   = $request->get('original_id');

    //     if(auth()->user()->position=="Dean"){
    //         $character = '';
    //     }else if(auth()->user()->position=="HoD"){
    //         $character = '/hod';
    //     }else if(auth()->user()->position=="Lecturer"){
    //         $character = '/lecturer';
    //     }

    //     $subjects = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('course_id', '=', $course_id)
    //              ->get();

    //     $result = "";
    //     if($value!=""){
    //     	$data_word = DB::table('assessment_final')
    //        			->join('ass_final','ass_final.fx_id','=','assessment_final.fx_id')
    //                 ->join('courses','courses.course_id','=','ass_final.course_id')
    //                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                 ->select('courses.*','semesters.*','ass_final.*','assessment_final.*')
    //                 ->where('courses.course_id','=',$course_id)
    //                 ->Where(function($query) use ($value) {
    //                     $query->orWhere('assessment_final.ass_fx_word','LIKE','%'.$value.'%');
    //                 })
    //                 ->where('courses.status', '=', 'Active')
    //                 ->where('assessment_final.status', '=', 'Active')
    //                 ->orderByDesc('semesters.semester_name')
    //                 ->orderBy('ass_final.assessment_name')
    //                 ->get();

    //         if(count($data_word)>0) {
    //             $result .= '<input type="hidden" id="data" value="word">';
    //             foreach($data_word as $ass_row_word){
    //             	$result .= '<div class="col-12 row align-self-center" id="course_list">';
    //                 $result .= '<div class="col-9 row align-self-center" >';
    //                 $result .= '<div class="checkbox_style align-self-center">';
    //                 $result .= '<input type="checkbox" value="'.$ass_row_word->ass_fx_id.'" class="group_q group_download">';
    //                 $result .= '</div>';
    //                 $result .= '<a href="'.$character.'/PastYear/images/final_assessment/'.$original_id."-".$ass_row_word->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$ass_row_word->semester_name.' : '.$ass_row_word->assessment_name.' / '.$ass_row_word->ass_fx_type.' / '.$ass_row_word->ass_fx_name.' <br> <a href='.$character."/PastYear/final_assessment/view/whole_paper/".$original_id."-".$ass_row_word->fx_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
    //                 $result .= '<div class="col-1" style="position: relative;top: -2px;">';
    //                 $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
    //                 $result .= '</div>';
    //                 $result .= '<div class="col-10" id="course_name">';
    //                 $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$ass_row_word->semester_name." : ".$ass_row_word->assessment_name." / ".$ass_row_word->ass_fx_type." / ".$ass_row_word->ass_fx_name.'</b></p>';
    //                 $result .= '</div>';
    //                 $result .= '</a>';
    //                 $result .= '</div>';
    //                 $result .= '<div class="col-3" id="course_action_two">';
	   //              $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$ass_row_word->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
	   //              $result .= '</div>';
    //                 $result .= '</div>';
    //            	}
    //        	}else{
    //            	$result .= '<div class="col-md-12" style="position:relative;top:5px;left:5px;">';
	   //          $result .= '<p>Not Found</p>';
	   //          $result .= '</div>';
    //         }
    //     }else{
    //     	$ass_final = DB::table('ass_final')
    //              ->select('ass_final.*')
    //              ->where('ass_final.course_id', '=', $course_id)
    //              ->where('status', '=', 'Active')
    //              ->get();

    //         foreach($ass_final as $row){
    //         	$result .= '<div class="col-12 row align-self-center" id="course_list">';
	   //          $result .= '<div class="col-12 row align-self-center">';
	   //          $result .= '<div class="checkbox_style align-self-center">';
	   //          $result .= '<input type="checkbox" value="'.$row->fx_id.'" class="group_download">';
	   //          $result .= '</div>';
	   //          $result .= '<a href="'.$character.'/PastYear/FinalAssessment/'.$original_id.'/list/'.$row->fx_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
	   //          $result .= '<div class="col-1" style="position: relative;top: -2px;">';
	   //          $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
	   //          $result .= '</div>';
	   //          $result .= '<div class="col-10" id="assessment_name">';
	   //          $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->assessment_name.'</b></p>';
	   //          $result .= '</div>';
	   //      	$result .= '</a>';
	   //          $result .= '</div>';
	   //          $result .= '</div>';
    //         }
    //     }
    //     return $result;
    // }

  //   public function zipFileDownloadName($course_id,$download)
  //   {
  //   	if($download == "All"){
  //           $string = explode('---',$course_id);
  //           $original_id = $string[0];
  //           $f_course_id = $string[1];
  //       }else{
  //           $string = explode('---',$course_id);
  //           $original_id = $string[0];
  //           $f_course_id = $string[1];
  //       }

  //       $subjects = DB::table('courses')
  //                ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
  //                ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
  //                ->select('courses.*','subjects.*','semesters.*')
  //                ->where('course_id', '=', $f_course_id)
  //                ->get();

  //       $ZipFile_name = $subjects[0]->subject_code." ".$subjects[0]->subject_name." ( ".$subjects[0]->semester_name." )";
  //       $zip = new ZipArchive;
  //       $fileName = storage_path('private/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
  //       $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
  //       $files = File::files(storage_path('/private/Assessment_Final/'));

  //       if($download == "checked"){
  //          	for($i=2;$i<(count($string)-1);$i++){

  //          		$AssFinal = AssFinal::where('fx_id','=',$string[$i])->firstOrFail();
		// 		$course_id = $AssFinal->course_id;

  //               $zip->addEmptyDir($AssFinal->assessment_name);
  //               $result_list = DB::table('assessment_final')
  //                        ->select('assessment_final.*')
  //                        ->where('assessment_final.fx_id', '=', $AssFinal->fx_id)
  //                        ->where('assessment_final.status','=','Active')
  //                        ->get();
  //               foreach($result_list as $rl_row){
		//             if($rl_row->ass_fx_type=="Question"){
		//                 $zip->addEmptyDir($AssFinal->assessment_name."/Question");
		//             }else{
		//                 $zip->addEmptyDir($AssFinal->assessment_name."/Solution"); 
		//             }
		//             foreach ($files as $key => $value) {
		//                 $relativeNameInZipFile = basename($value);
		//                 if($rl_row->ass_fx_document == $relativeNameInZipFile){
		//                     $ext = explode('.',$relativeNameInZipFile);
		//                     if($rl_row->ass_fx_type=="Question"){
		//                         $zip->addFile($value,$AssFinal->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
		//                     }else{
		//                         $zip->addFile($value,$AssFinal->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
		//                     } 
		//                 }
		//             }
		//         }
		//    	}
		// }else if($download == "searchedWord"){
		// 	for($i=2;$i<(count($string)-1);$i++){

		// 		$assessment_final = AssessmentFinal::where('ass_fx_id','=',$string[$i])->firstOrFail();
		// 		$fx_id = $assessment_final->fx_id;

		// 		$AssFinal = AssFinal::where('fx_id','=',$fx_id)->firstOrFail();
		// 		$course_id = $AssFinal->course_id;

		// 		$course = DB::table('courses')
  //                ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
  //                ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
  //                ->select('courses.*','subjects.*','semesters.*')
  //                ->where('course_id', '=', $course_id)
  //                ->get();

  //                $zip->addEmptyDir($AssFinal->assessment_name);

		//          if($assessment_final->ass_fx_type=="Question"){
		//             $zip->addEmptyDir($AssFinal->assessment_name."/Question");
		//          }else{
		//             $zip->addEmptyDir($AssFinal->assessment_name."/Solution"); 
		//          }
		//          foreach ($files as $key => $value) {
		//            	$relativeNameInZipFile = basename($value);
		//             if($assessment_final->ass_fx_document == $relativeNameInZipFile){
		//                 $ext = explode('.',$relativeNameInZipFile);
		//                 if($assessment_final->ass_fx_type=="Question"){
		//                     $zip->addFile($value,$AssFinal->assessment_name."/Question/".$assessment_final->ass_fx_name.'.'.$ext[1]);
		//                 }else{
		//                     $zip->addFile($value,$AssFinal->assessment_name."/Solution/".$assessment_final->ass_fx_name.'.'.$ext[1]);
		//                 } 
		//             }
		//         }
		// 	}
		// }else{
	 //        $ass_final = DB::table('ass_final')
		// 	                 ->select('ass_final.*')
		// 	                 ->where('course_id', '=', $string[1])
		// 	                 ->where('status', '=', 'Active')
		// 	                 ->get();

	 //        foreach($ass_final as $ass_row){
	 //                	$zip->addEmptyDir($ass_row->assessment_name);
	 //                	$result_list = DB::table('assessment_final')
	 //                         ->select('assessment_final.*')
	 //                         ->where('assessment_final.fx_id', '=', $ass_row->fx_id)
	 //                         ->where('assessment_final.status','=','Active')
	 //                         ->get();
	 //            foreach($result_list as $rl_row){
		// 	                if($rl_row->ass_fx_type=="Question"){
		// 	                    $zip->addEmptyDir($ass_row->assessment_name."/Question");
		// 	                }else{
		// 	                    $zip->addEmptyDir($ass_row->assessment_name."/Solution"); 
		// 	                }
		// 	        foreach ($files as $key => $value) {
		// 	            $relativeNameInZipFile = basename($value);
		// 	            if($rl_row->ass_fx_document == $relativeNameInZipFile){
		// 	                $ext = explode('.',$relativeNameInZipFile);
		// 	                if($rl_row->ass_fx_type=="Question"){
		// 	                    $zip->addFile($value,$ass_row->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
		// 	                }else{
		// 	                    $zip->addFile($value,$ass_row->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
		// 	                } 
		// 	            }
		// 	        }
		// 	    }
	 //        }
		// }
		// $zip->close();
  //   	if($this->checkCoursePerson($original_id)==true){
  //           return response()->download($fileName)->deleteFileAfterSend(true);
  //       }else{
  //           Storage::disk('private')->delete('/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
  //           return redirect()->back();
  //       }
  //   }

    public function PastYearAssessmentList($id,$course_id)
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

        $AssFinal = AssFinal::where('course_id','=',$course_id)->firstOrFail();

		$previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $course_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('ass_fx_type')
                    ->get();

        $assessment_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_type')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->orderBy('assessment_final.ass_fx_id')
                    ->get();

        if(count($course)>0){
            return view('dean.PastYear.viewFinalAssessmentList',compact('course','AssFinal','previous','assessment_list','group_list'));
        }else{
            return redirect()->back();
        }
    }

    public function searchAssessmentlist(Request $request)
    {
    	$value         = $request->get('value');
        $fx_id         = $request->get('fx_id');
        $original_id   = $request->get('original_id');

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $AssFinal = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();

        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $AssFinal->course_id)
                 ->get();

        $result = "";
        if($value!=""){
            $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('assessment_final.course_id', '=', $AssFinal->course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_final.ass_fx_name','LIKE','%'.$value.'%')
                            ->orWhere('assessment_final.ass_fx_word','LIKE','%'.$value.'%');
                    })
                    ->where('assessment_final.status', '=', 'Active')
                    ->groupBy('assessment_final.ass_fx_type')
                    ->get();

            $assessment_final = DB::table('assessment_final')
                        ->select('assessment_final.*')
                        ->where('assessment_final.course_id', '=', $AssFinal->course_id)
                        ->where('assessment_final.status', '=', 'Active')
                        ->Where(function($query) use ($value) {
                            $query->orWhere('assessment_final.ass_fx_name','LIKE','%'.$value.'%')
                                ->orWhere('assessment_final.ass_fx_word','LIKE','%'.$value.'%');
                        })
                        ->orderBy('assessment_final.ass_fx_type')
                        ->orderBy('assessment_final.ass_fx_name')
                        ->orderBy('assessment_final.ass_fx_id')
                        ->get();
            $i=0;
            if(count($group_list)>0) {
                foreach($group_list as $row_group){
                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<div class="col-12 row" style="padding:10px;margin: 0px;">';
                    $result .= '<div class="checkbox_group_style">';
                    $result .= '<input type="checkbox" id="group_'.$row_group->ass_fx_type.'" class="group_checkbox">';
                    $result .= '</div>';
                    $result .= '<h5 class="group plus" id="'.$i.'">'.$row_group->ass_fx_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_final as $row){
                        if($row_group->ass_fx_type == $row->ass_fx_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/PastYear/images/final_assessment/'.$original_id.'-'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$previous[0]->semester_name.' : Final / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='.$character."/PastYear/final_assessment/view/whole_paper/".$original_id.'-'.$row->course_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_fx_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
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
            $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $AssFinal->course_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('ass_fx_type')
                    ->get();

            $assessment_final = DB::table('assessment_final')
                        ->select('assessment_final.*')
                        ->where('course_id', '=', $AssFinal->course_id)
                        ->where('status', '=', 'Active')
                        ->orderBy('assessment_final.ass_fx_type')
                        ->orderBy('assessment_final.ass_fx_name')
                        ->orderBy('assessment_final.ass_fx_id')
                        ->get();
            $i = 0;
            if(count($group_list)>0) {
                foreach($group_list as $row_group){
                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<div class="col-12 row" style="padding:10px;margin: 0px;">';
                    $result .= '<div class="checkbox_group_style">';
                    $result .= '<input type="checkbox" id="group_'.$row_group->ass_fx_type.'" class="group_checkbox">';
                    $result .= '</div>';
                    $result .= '<h5 class="group plus" id="'.$i.'">'.$row_group->ass_fx_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_final as $row){
                        if($row_group->ass_fx_type == $row->ass_fx_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/PastYear/images/final_assessment/'.$original_id.'-'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$previous[0]->semester_name.' : Final / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='.$character."/PastYear/final_assessment/view/whole_paper/".$original_id.'-'.$row->course_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_fx_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
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

   	public function zipFileDownloadList($fx_id,$download)
    {
    	if($download == "checked"){
            $string = explode('---',$fx_id);
            $f_fx_id = $string[1];
            $f_course_id = $string[0];
        }else{
            $string = explode('---',$fx_id);
            $f_fx_id = $string[1];
            $f_course_id = $string[0];
        }

        $AssFinal = AssFinal::where('fx_id', '=', $f_fx_id)->firstOrFail();
        $course_id = $AssFinal->course_id;

        $subjects = DB::table('subjects')
                        ->join('courses','courses.subject_id','=','subjects.subject_id')
                        ->join('semesters','courses.semester','=','semesters.semester_id')
                        ->select('courses.*','subjects.*','semesters.*')
                        ->where('courses.course_id', '=', $course_id)
                        ->get();

        $ZipFile_name = $subjects[0]->semester_name." Final ( ".$subjects[0]->subject_code." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment_Final/'));
        
        if($download == "checked"){
            for($i=2;$i<(count($string)-1);$i++){
                $studentId_By = explode('_',$string[$i]);
                $zip->addEmptyDir($studentId_By[1]);
                $assessment_final = AssessmentFinal::where('ass_fx_id', '=', $studentId_By[0])->firstOrFail();
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($assessment_final->ass_fx_document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        $zip->addFile($value,$studentId_By[1]."/".$assessment_final->ass_fx_name.'.'.$ext[1]);
                    }
                }
            }
        }else{
            $result_list = DB::table('assessment_final')
                         ->select('assessment_final.*')
                         ->where('assessment_final.course_id', '=', $course_id)
                         ->where('assessment_final.status','=','Active')
                         ->get();

            foreach($result_list as $rl_row){
                if($rl_row->ass_fx_type=="Question"){
                    $zip->addEmptyDir("Question");
                }else{
                    $zip->addEmptyDir("Solution"); 
                }
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($rl_row->ass_fx_document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($rl_row->ass_fx_type=="Question"){
                            $zip->addFile($value,"Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
                        }else{
                            $zip->addFile($value,"Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
                        } 
                    }
                }
            } 
        }
        $zip->close();
        if($this->checkCoursePerson($f_course_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
            return redirect()->back();
        }
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
            $batch_list = DB::table('assessment_final_result')
			                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
			                 ->join('courses','assessment_final_result.course_id','=','courses.course_id')
                             ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
			                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
			                 ->select('assessment_final_result.*','students.*','courses.*','semesters.*','subjects.*')
                             ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
			                 ->where('courses.course_id','!=',$course_id)
			                 ->Where(function($query) use ($value) {
			                    $query->orWhere('students.batch','LIKE','%'.$value.'%')
			                    	->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
			                 })
			                 ->where('assessment_final_result.status','=','Active')
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
		           	$result .= '<input type="checkbox" value="'.$rs_row->fxr_id.'" class="group_r group_download">';
		            $result .= '</div>';
		            $result .= '<a href="'.$character.'/PastYear/FinalSampleResult/'.$course_id.'/previous/'.$rs_row->course_id.'/'.$rs_row->batch.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
		            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
		            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
		            $result .= '</div>';
		            $result .= '<div class="col-10" id="course_name">';
		            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$rs_row->semester_name." : Final Result List ( ".$rs_row->batch.' ) </b></p>';
		            $result .= '</div>';
		        	$result .= '</a>';
		            $result .= '</div>';
		            $result .= '</div>';
	           	}
	        }else{
	           	$student_list = DB::table('assessment_final_result')
				                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
				                 ->join('users','users.user_id', '=', 'students.user_id')
				                 ->join('courses','assessment_final_result.course_id','=','courses.course_id')
                                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
				                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
				                 ->select('assessment_final_result.*','students.*','users.*','courses.*','semesters.*','subjects.*')
				                 ->where('courses.course_id','!=',$course_id)
                                 ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
				                 ->Where(function($query) use ($value) {
				                    $query->orWhere('assessment_final_result.student_id','LIKE','%'.$value.'%')
				                        ->orWhere('students.batch','LIKE','%'.$value.'%')
				                        ->orWhere('users.name','LIKE','%'.$value.'%');
				                 })
				                 ->where('assessment_final_result.status','=','Active')
				                 ->groupBy('assessment_final_result.student_id')
				                 ->groupBy('courses.semester')
				                 ->orderByDesc('semesters.semester_name')
				                 ->get();
				if(count($student_list)>0) {
				   	$result .= '<input type="hidden" id="data" value="student">';
					foreach($student_list as $stu_row){
		          		$result .= '<div class="col-12 row align-self-center" id="course_list">';
			            $result .= '<div class="col-8 row align-self-center">';
			            $result .= '<div class="checkbox_style align-self-center">';
			            $result .= '<input type="checkbox" value="'.$stu_row->fxr_id.'" class="group_r group_download">';
			            $result .= '</div>';
			            $result .= '<a href="'.$character.'/PastYear/FinalSampleResult/'.$course_id.'/result/'.$stu_row->fxr_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
			            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
			            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
			            $result .= '</div>';
			            $result .= '<div class="col-10" id="course_name">';
			            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$stu_row->semester_name." : Final Result List ( ".$stu_row->student_id." ".$stu_row->name.' ) </b></p>';
			            $result .= '</div>';
			        	$result .= '</a>';
			            $result .= '</div>';
			            $result .= '</div>';
		           	}
	           	}else{
	           		$submitted_list = DB::table('assessment_final_result')
				                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
				                 ->join('users','users.user_id', '=', 'students.user_id')
				                 ->join('courses','assessment_final_result.course_id','=','courses.course_id')
				                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
				                 ->select('assessment_final_result.*','students.*','users.*','courses.*','semesters.*','subjects.*')
				                 ->where('courses.course_id','!=',$course_id)
                                 ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
				                 ->Where(function($query) use ($value) {
				                    $query->orWhere('assessment_final_result.submitted_by','LIKE','%'.$value.'%');
				                 })
				                 ->where('assessment_final_result.status','=','Active')
				                 ->groupBy('assessment_final_result.submitted_by')
				                 ->groupBy('courses.semester')
				                 ->orderByDesc('semesters.semester_name')
				                 ->get();
				    if(count($submitted_list)>0){
				        $result .= '<input type="hidden" id="data" value="submitted_by">';
					    foreach($submitted_list as $sub_row){
			          		$result .= '<div class="col-12 row align-self-center" id="course_list">';
				            $result .= '<div class="col-8 row align-self-center">';
				            $result .= '<div class="checkbox_style align-self-center">';
				            $result .= '<input type="checkbox" value="'.$sub_row->fxr_id.'" class="group_r group_download">';
				            $result .= '</div>';
				            $result .= '<a href="'.$character.'/PastYear/FinalSampleResult/'.$course_id.'/previous/'.$sub_row->course_id.'/'.$sub_row->submitted_by.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
				            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
				            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
				            $result .= '</div>';
				            $result .= '<div class="col-10" id="course_name">';
				            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$sub_row->semester_name.' : Final Result List ( '.$sub_row->submitted_by.' )</b></p>';
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
        	$previous_result_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->join('assessment_final_result','courses.course_id','=','assessment_final_result.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

            foreach($previous_result_semester as $row){
           		$result .= '<div class="col-12 row align-self-center" id="course_list">';
	            $result .= '<div class="col-8 row align-self-center">';
	            $result .= '<div class="checkbox_style align-self-center">';
	            $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_r group_download">';
	            $result .= '</div>';
	            $result .= '<a href="'.$character.'/PastYear/FinalSampleResult/'.$course_id.'/previous/'.$row->course_id.'/All/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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

    public function zipFileDownloadResult($course_id,$download)
    {
        if($download!="All"){
            $string = explode('---',$course_id);
            $f_course_id = $string[0];
        }else{
            $f_course_id = $course_id;
        }

        $subjects = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $f_course_id)
                 ->get();

        $ZipFile_name = $subjects[0]->subject_name." ( ".$subjects[0]->subject_code." )";
        $zip = new ZipArchive;
	    $fileName = storage_path('private/Final_Result/PastYear/'.$ZipFile_name.'.zip');
	    $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
	    $Resultfiles = File::files(storage_path('/private/Final_Result/'));
        if($download == "checked"){
        	for($i=1;$i<(count($string)-1);$i++){

        		$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $string[$i])
                 ->get();

        		$zip->addEmptyDir($course[0]->semester_name);

        		$assessment_final_result = DB::table('assessment_final_result')
			                 ->select('assessment_final_result.*')
			                 ->where('course_id', '=', $course[0]->course_id)
			                 ->where('status', '=', 'Active')
			                 ->groupBy('assessment_final_result.student_id')
			                 ->get();

			    foreach($assessment_final_result as $ass_row){
	                $zip->addEmptyDir($course[0]->semester_name."/".$ass_row->student_id);

                    $result_list = DB::table('assessment_final_result')
                                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                                 ->join('users','users.user_id', '=', 'students.user_id')
                                 ->select('assessment_final_result.*','students.*','users.*')
                                 ->where('assessment_final_result.course_id', '=', $course[0]->course_id)
                                 ->where('assessment_final_result.status','=','Active')
                                 ->where('assessment_final_result.student_id','=',$ass_row->student_id)
                                 ->get();

                    foreach($result_list as $rl_row){
	                    if($rl_row->submitted_by=="Lecturer"){
	                        $zip->addEmptyDir($course[0]->semester_name."/".$ass_row->student_id."/Lecturer");
	                    }else{
	                        $zip->addEmptyDir($course[0]->semester_name."/".$ass_row->student_id."/Students"); 
	                    }
	                    foreach ($Resultfiles as $key => $value) {
	                        $relativeNameInZipFile = basename($value);
	                        if($rl_row->document == $relativeNameInZipFile){
	                            $ext = explode('.',$relativeNameInZipFile);
	                            if($rl_row->submitted_by=="Lecturer"){
	                               	$zip->addFile($value,$course[0]->semester_name."/".$ass_row->student_id."/Lecturer/".$rl_row->document_name.'.'.$ext[1]);
	                            }else{
	                               	$zip->addFile($value,$course[0]->semester_name."/".$ass_row->student_id."/Students/".$rl_row->document_name.'.'.$ext[1]);
	                            } 
	                        }
	                    }
	                }
	            }
        	}
        }else if($download == "batch"){
        	for($i=1;$i<(count($string)-1);$i++){

	        	$assessment_result = AssessmentFinalResult::where('fxr_id','=',$string[$i])->firstOrFail();
				$course_id = $assessment_result->course_id;
				$student_id = $assessment_result->student_id;

				$student = Student::where('student_id','=',$student_id)->firstOrFail();
				$batch = $student->batch;

				$list = DB::table('assessment_final_result')
				 ->join('courses','assessment_final_result.course_id','=','courses.course_id')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*','assessment_final_result.*')
                 ->where('courses.course_id', '=', $course_id)
                 ->where('assessment_final_result.status', '=', "Active")
                 ->get();

                foreach($list as $row_list){
                	$zip->addEmptyDir($list[0]->semester_name." ( ".$batch." )/");

                	$group_result = DB::table('assessment_final_result')
                         ->join('students','students.student_id','=','assessment_final_result.student_id')
                         ->join('courses','courses.course_id','=','assessment_final_result.course_id')
                         ->select('assessment_final_result.*','courses.*')
                         ->where('assessment_final_result.course_id','=',$row_list->course_id)
                         ->where('students.batch','=',$batch)
                         ->where('assessment_final_result.status','=','Active')
                         ->groupBy('assessment_final_result.student_id')
                         ->get();
                    foreach($group_result as $row_stu){
                    	$zip->addEmptyDir($list[0]->semester_name." ( ".$batch." )/".$row_stu->student_id);

                    	$result_list = DB::table('assessment_final_result')
                         			 ->join('courses','courses.course_id','=','assessment_final_result.course_id')
	                                 ->select('assessment_final_result.*','courses.*')
	                                 ->where('assessment_final_result.course_id', '=', $row_list->course_id)
	                                 ->where('assessment_final_result.status','=','Active')
	                                 ->where('assessment_final_result.student_id','=',$row_stu->student_id)
	                                 ->get();

		                foreach($result_list as $rl_row){
			                if($rl_row->submitted_by=="Lecturer"){
			                    $zip->addEmptyDir($list[0]->semester_name." ( ".$batch." )/".$row_stu->student_id."/Lecturer");
			                }else{
			                    $zip->addEmptyDir($list[0]->semester_name." ( ".$batch." )/".$row_stu->student_id."/Students"); 
			                }
			               	foreach ($Resultfiles as $key => $value) {
			                    $relativeNameInZipFile = basename($value);
			               	    if($rl_row->document == $relativeNameInZipFile){
			                        $ext = explode('.',$relativeNameInZipFile);
			               	        if($rl_row->submitted_by=="Lecturer"){
			                            $zip->addFile($value,$list[0]->semester_name." ( ".$batch." )/".$row_stu->student_id."/Lecturer/".$rl_row->document_name.'.'.$ext[1]);
			               	        }else{
			                            $zip->addFile($value,$list[0]->semester_name." ( ".$batch." )/".$row_stu->student_id."/Students/".$rl_row->document_name.'.'.$ext[1]);
			               	        } 
			               	    }
			               	}
			            }
                    }
                }
            }
        }else if($download == "student"){
        	for($i=1;$i<(count($string)-1);$i++){

        		$assessment_result = AssessmentFinalResult::where('fxr_id','=',$string[$i])->firstOrFail();
				$course_id = $assessment_result->course_id;
				$student_id = $assessment_result->student_id;

				$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

                $zip->addEmptyDir($course[0]->semester_name." ( ".$student_id." )/");

                $group_result = DB::table('assessment_final_result')
                         ->join('courses','courses.course_id','=','assessment_final_result.course_id')
                         ->select('assessment_final_result.*','courses.*')
                         ->where('assessment_final_result.student_id', '=', $student_id)
                         ->where('courses.semester','=',$course[0]->semester_id)
                         ->where('assessment_final_result.status','=','Active')
                         ->groupBy('assessment_final_result.student_id')
                         ->get();

                foreach($group_result as $row){
                   	$zip->addEmptyDir($course[0]->semester_name." ( ".$student_id." )/");

	                $result_list = DB::table('assessment_final_result')
                         			 ->join('courses','courses.course_id','=','assessment_final_result.course_id')
	                                 ->select('assessment_final_result.*','courses.*')
	                                 ->where('assessment_final_result.course_id', '=', $row->course_id)
	                                 ->where('assessment_final_result.status','=','Active')
	                                 ->where('assessment_final_result.student_id','=',$row->student_id)
	                                 ->get();

	                foreach($result_list as $rl_row){
		                if($rl_row->submitted_by=="Lecturer"){
		                    $zip->addEmptyDir($course[0]->semester_name." ( ".$student_id." )/"."Lecturer");
		                }else{
		                    $zip->addEmptyDir($course[0]->semester_name." ( ".$student_id." )/"."Students"); 
		                }
		               	foreach ($Resultfiles as $key => $value) {
		                    $relativeNameInZipFile = basename($value);
		               	    if($rl_row->document == $relativeNameInZipFile){
		                        $ext = explode('.',$relativeNameInZipFile);
		               	        if($rl_row->submitted_by=="Lecturer"){
		                            $zip->addFile($value,$course[0]->semester_name." ( ".$student_id." )/"."Lecturer/".$rl_row->document_name.'.'.$ext[1]);
		               	        }else{
		                            $zip->addFile($value,$course[0]->semester_name." ( ".$student_id." )/"."Students/".$rl_row->document_name.'.'.$ext[1]);
		               	        } 
		               	    }
		               	}
		            }
	        	}
        	}
        }else if($download == "submitted_by"){
        	for($i=1;$i<(count($string)-1);$i++){
        		$assessment_result = AssessmentFinalResult::where('fxr_id','=',$string[$i])->firstOrFail();
				$course_id = $assessment_result->course_id;
				$submitted_by = $assessment_result->submitted_by;

				$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

                $zip->addEmptyDir($course[0]->semester_name." ( ".$submitted_by." )/");

                $group_result = DB::table('assessment_final_result')
                         ->join('courses','courses.course_id','=','assessment_final_result.course_id')
                         ->select('assessment_final_result.*','courses.*')
                         ->where('courses.semester','=',$course[0]->semester_id)
                         ->where('assessment_final_result.submitted_by','=',$submitted_by)
                         ->where('assessment_final_result.status','=','Active')
                         ->groupBy('assessment_final_result.student_id')
                         ->get();

                foreach($group_result as $row){
                   	$zip->addEmptyDir($course[0]->semester_name." ( ".$submitted_by." )/");

	                $result_list = DB::table('assessment_final_result')
                         			 ->join('courses','courses.course_id','=','assessment_final_result.course_id')
	                                 ->select('assessment_final_result.*','courses.*')
	                                 ->where('assessment_final_result.course_id', '=', $row->course_id)
	                                 ->where('assessment_final_result.status','=','Active')
	                                 ->where('assessment_final_result.submitted_by','=',$submitted_by)
                                     ->where('assessment_final_result.student_id','=',$row->student_id)
	                                 ->get();

	                foreach($result_list as $rl_row){
		                if($rl_row->submitted_by==$submitted_by){
		                    $zip->addEmptyDir($course[0]->semester_name." ( ".$submitted_by." )/".$row->student_id);
		                }
		               	foreach ($Resultfiles as $key => $value) {
		                    $relativeNameInZipFile = basename($value);
		               	    if($rl_row->document == $relativeNameInZipFile){
		                        $ext = explode('.',$relativeNameInZipFile);
		               	        if($rl_row->submitted_by==$submitted_by){
		                            $zip->addFile($value,$course[0]->semester_name." ( ".$submitted_by." )/".$row->student_id."/".$rl_row->document_name.'.'.$ext[1]);
		               	        }
		               	    }
		               	}
		            }
	        	}
        	}
        }else{
            $assessment_result = AssessmentFinalResult::where('course_id','=',$f_course_id)->firstOrFail();
            $course_id = $assessment_result->course_id;
            $submitted_by = $assessment_result->submitted_by;

            $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

            $zip->addEmptyDir($course[0]->semester_name." ( ".$submitted_by." )/");

            $group_result = DB::table('assessment_final_result')
                         ->join('courses','courses.course_id','=','assessment_final_result.course_id')
                         ->select('assessment_final_result.*','courses.*')
                         ->where('courses.semester','=',$course[0]->semester_id)
                         ->where('assessment_final_result.submitted_by','=',$submitted_by)
                         ->where('assessment_final_result.status','=','Active')
                         ->groupBy('assessment_final_result.student_id')
                         ->get();

            foreach($group_result as $row){
                $zip->addEmptyDir($course[0]->semester_name." ( ".$submitted_by." )/");

                $result_list = DB::table('assessment_final_result')
                                     ->join('courses','courses.course_id','=','assessment_final_result.course_id')
                                     ->select('assessment_final_result.*','courses.*')
                                     ->where('assessment_final_result.course_id', '=', $row->course_id)
                                     ->where('assessment_final_result.status','=','Active')
                                     ->where('assessment_final_result.submitted_by','=',$submitted_by)
                                     ->where('assessment_final_result.student_id','=',$row->student_id)
                                     ->get();

                foreach($result_list as $rl_row){
                    if($rl_row->submitted_by==$submitted_by){
                        $zip->addEmptyDir($course[0]->semester_name." ( ".$submitted_by." )/".$row->student_id);
                    }
                    foreach ($Resultfiles as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->document == $relativeNameInZipFile){
                                $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->submitted_by==$submitted_by){
                                $zip->addFile($value,$course[0]->semester_name." ( ".$submitted_by." )/".$row->student_id."/".$rl_row->document_name.'.'.$ext[1]);
                            }
                        }
                    }
                }
            }
        }
        $zip->close();
    	if($this->checkCoursePerson($f_course_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Final_Result/PastYear/'.$ZipFile_name.'.zip');
            return redirect()->back();
        }
    }

    public function PastYearStudentList($id,$course_id,$search)
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
                 ->where('course_id', '=', $course_id)
                 ->get();

   		$lecturer_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        $student_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->where('assessment_final_result.submitted_by','=', 'Students')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        if(count($course)>0){
            return view('dean.PastYear.viewSRFinalStudentList',compact('search','id','course','lecturer_result','student_result','previous'));
        }else{
            return redirect()->back();
        }
    }

    public function searchStudentList(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');
        $id            = $request->get('id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $result = "";
        if($value!=""){
            $result_list = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->Where(function($query) use ($value) {
                    $query->orWhere('assessment_final_result.student_id','LIKE','%'.$value.'%')
                    	->orWhere('assessment_final_result.submitted_by','LIKE','%'.$value.'%')
                        ->orWhere('students.batch','LIKE','%'.$value.'%')
                        ->orWhere('users.name','LIKE','%'.$value.'%');
                 })
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

            $check_submitted_list = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->Where(function($query) use ($value) {
                    $query->orWhere('assessment_final_result.submitted_by','LIKE','%'.$value.'%');
                 })
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
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
                    $result .= '<a href="'.$character.'/PastYear/FinalSampleResult/'.$id.'/result/'.$row->fxr_id.'" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
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
            $lecturer_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

            $student_result = DB::table('assessment_final_result')
                     ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                     ->join('users','users.user_id', '=', 'students.user_id')
                     ->select('assessment_final_result.*','students.*','users.*')
                     ->where('assessment_final_result.course_id', '=', $course_id)
                     ->where('assessment_final_result.submitted_by','=', 'Students')
                     ->where('assessment_final_result.status','=','Active')
                     ->groupBy('assessment_final_result.student_id')
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
                    $result .= '<a href="'.$character.'/PastYear/sampleResult/'.$id.'/result/'.$row->fxr_id.'" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
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
                    $result .= '<a href="'.$character.'/PastYear/sampleResult/'.$id.'/result/'.$sow->fxr_id.'" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
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

    public function zipFileDownloadStudent($course_id,$download)
    {
        if($download == "checked"){
            $string = explode('---',$course_id);
            $original_id = $string[0];
            $f_course_id = $string[1];
        }else{
            $string = explode('---',$course_id);
            $original_id = $string[0];
            $f_course_id = $string[1];
        }

        $subjects = DB::table('subjects')
                        ->join('courses','courses.subject_id','=','subjects.subject_id')
                        ->join('semesters','semesters.semester_id','=','courses.semester')
                        ->select('courses.*','subjects.*','semesters.*')
                        ->where('courses.course_id', '=', $f_course_id)
                        ->get();

        $ZipFile_name = "Final Sample Result ( ".$subjects[0]->semester_name." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Final_Result/PastYear/'.$ZipFile_name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Final_Result/'));
        
        if($download == "checked"){
            for($i=2;$i<(count($string)-1);$i++){
                $studentId_By = explode('_',$string[$i]);
                $zip->addEmptyDir($studentId_By[0]);
                if($studentId_By[1]=="All"){
                  $result_list = DB::table('assessment_final_result')
                         ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                         ->join('users','users.user_id', '=', 'students.user_id')
                         ->select('assessment_final_result.*','students.*','users.*')
                         ->where('assessment_final_result.course_id', '=', $f_course_id)
                         ->where('assessment_final_result.status','=','Active')
                         ->where('assessment_final_result.student_id','=',$studentId_By[0])
                         ->get();
                }else{
                  $result_list = DB::table('assessment_final_result')
                         ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                         ->join('users','users.user_id', '=', 'students.user_id')
                         ->select('assessment_final_result.*','students.*','users.*')
                         ->where('assessment_final_result.course_id', '=', $f_course_id)
                         ->where('assessment_final_result.status','=','Active')
                         ->where('assessment_final_result.submitted_by','=',$studentId_By[1])
                         ->where('assessment_final_result.student_id','=',$studentId_By[0])
                         ->get();
                }
                foreach($result_list as $rl_row){
                    if($rl_row->submitted_by=="Lecturer"){
                        $zip->addEmptyDir($studentId_By[0]."/Lecturer");
                    }else{
                        $zip->addEmptyDir($studentId_By[0]."/Students"); 
                    }
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->document == $relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->submitted_by=="Lecturer"){
                                $zip->addFile($value,$studentId_By[0]."/Lecturer/".$rl_row->document_name.'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$studentId_By[0]."/Students/".$rl_row->document_name.'.'.$ext[1]);
                            }
                        }
                    }
                }
            }
        }else{
            $group_result = DB::table('assessment_final_result')
                         ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                         ->join('users','users.user_id', '=', 'students.user_id')
                         ->select('assessment_final_result.*','students.*','users.*')
                         ->where('assessment_final_result.course_id', '=', $f_course_id)
                         ->where('assessment_final_result.status','=','Active')
                         ->groupBy('assessment_final_result.student_id')
                         ->get();
            
            foreach($group_result as $row){
                $zip->addEmptyDir($row->student_id);
                $result_list = DB::table('assessment_final_result')
                         ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                         ->join('users','users.user_id', '=', 'students.user_id')
                         ->select('assessment_final_result.*','students.*','users.*')
                         ->where('assessment_final_result.course_id', '=', $f_course_id)
                         ->where('assessment_final_result.status','=','Active')
                         ->where('assessment_final_result.student_id','=',$row->student_id)
                         ->get();
                foreach($result_list as $rl_row){
                    if($rl_row->submitted_by=="Lecturer"){
                        $zip->addEmptyDir($row->student_id."/Lecturer");
                    }else{
                        $zip->addEmptyDir($row->student_id."/Students"); 
                    }
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->document == $relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->submitted_by=="Lecturer"){
                                $zip->addFile($value,$row->student_id."/Lecturer/".$rl_row->document_name.'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$row->student_id."/Students/".$rl_row->document_name.'.'.$ext[1]);
                            } 
                        }
                    }
                } 
            }
        }
        $zip->close();
        if($this->checkCoursePerson($original_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Final_Result/PastYear/'.$ZipFile_name.'.zip');
            return redirect()->back();
        }
    }

    public function PastYearResultList($id,$fxr_id)
    {
    	$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment_final_result = AssessmentFinalResult::where('fxr_id', '=', $fxr_id)->firstOrFail();
        $course_id = $assessment_final_result->course_id;
        $student_id = $assessment_final_result->student_id;

        $check_course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $lecturer_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->where('assessment_final_result.student_id','=',$student_id)
                 ->orderBy('assessment_final_result.document_name')
                 ->get();

        $student_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $course_id)
                 ->where('assessment_final_result.submitted_by','=', 'Students')
                 ->where('assessment_final_result.status','=','Active')
                 ->where('assessment_final_result.student_id','=',$student_id)
                 ->orderBy('assessment_final_result.document_name')
                 ->get();

        if(count($check_course)>0){
            return view('dean.PastYear.viewSRFinalResultList',compact('id','course','assessment_final_result','lecturer_result','student_result'));
        }else{
            return redirect()->back();
        }
    }

    public function zipFileDownloadDocument($student_id,$course_id,$download)
    {
        if($download == "checked"){
            $string = explode('_',$course_id);
            $original_id = $string[0];
            $f_course_id = $string[1];
        }else{
            $string = explode('_',$course_id);
            $original_id = $string[0];
            $f_course_id = $string[1];
        }

        $ZipFile_name = "Final Exam Result ( ".$student_id." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Final_Result/PastYear/'.$ZipFile_name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Final_Result/'));

        if($download == "checked"){
            for($i=2;$i<(count($string)-1);$i++){
                $assessment_final_result = AssessmentFinalResult::where('fxr_id', '=', $string[$i])->firstOrFail();
                if($assessment_final_result->submitted_by=="Lecturer"){
                    $zip->addEmptyDir("Lecturer");
                }else{
                    $zip->addEmptyDir("Students"); 
                }
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($assessment_final_result->document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($assessment_final_result->submitted_by=="Lecturer"){
                            $zip->addFile($value,"Lecturer/".$assessment_final_result->document_name.'.'.$ext[1]);
                        }else{
                            $zip->addFile($value,"Students/".$assessment_final_result->document_name.'.'.$ext[1]);
                        } 
                    }
                }
            }
        }else{
            $result = DB::table('assessment_final_result')
                         ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                         ->join('users','users.user_id', '=', 'students.user_id')
                         ->select('assessment_final_result.*','students.*','users.*')
                         ->where('assessment_final_result.course_id', '=', $f_course_id)
                         ->where('assessment_final_result.status','=','Active')
                         ->where('assessment_final_result.student_id','=',$student_id)
                         ->get();

            foreach($result as $row){
                if($row->submitted_by=="Lecturer"){
                    $zip->addEmptyDir("Lecturer");
                }else{
                    $zip->addEmptyDir("Students"); 
                }
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($row->document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($row->submitted_by=="Lecturer"){
                            $zip->addFile($value,"Lecturer/".$row->document_name.'.'.$ext[1]);
                        }else{
                            $zip->addFile($value,"Students/".$row->document_name.'.'.$ext[1]);
                        } 
                    }
                }
            }
        }
        $zip->close();
        if($this->checkCoursePerson($original_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Final_Result/PastYear/'.$ZipFile_name.'.zip');
            return redirect()->back();
        }
    }

    public function FinalAssessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $string = explode('-',$image_name);

        $checkImageFXID = AssessmentFinal::where('ass_fx_document', '=', $string[1])->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $string[0])
                 ->get();

        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment_Final/' . $string[1]);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function view_wholePaper($fx_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $string = explode('-',$fx_id);
        $assessment_final = AssFinal::where('course_id', '=', $string[1])->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $string[0])
                 ->get();

        $assessment_list = DB::table('assessment_final')
                    ->join('courses', 'courses.course_id', '=', 'assessment_final.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessment_final.*','courses.*','semesters.*')
                    ->where('assessment_final.course_id', '=', $string[1])
                    ->where('assessment_final.status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_id')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        if(count($course)>0){
            return view('dean.FinalExam.viewWholePaper', compact('assessment_list','assessment_final','string'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadFiles($ass_fx_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $string = explode('-',$ass_fx_id);
        $assessment_final = AssessmentFinal::where('ass_fx_id', '=', $string[1])->firstOrFail();
        $course_id = $assessment_final->course_id;

        // $AssFinal = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        // $question = $AssFinal->assessment_name;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $string[0])
                 ->get();

        if(count($course)>0){
            $ext = "";
            if($assessment_final->ass_fx_document!=""){
                $ext = explode(".", $assessment_final->ass_fx_document);
            }

            return Storage::disk('private')->download('Assessment_Final/'.$assessment_final->ass_fx_document, "Final_".$assessment_final->ass_fx_type."_".$assessment_final->ass_fx_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function FinalResult_image($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;

        $string = explode('-',$image_name);

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $string[0])
                 ->get();

        if(count($course)>0){
            $storagePath = storage_path('/private/Final_Result/' . $string[1]);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function view_wholePaperResult($fxr_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $string = explode('-',$fxr_id);

        $checkCID = AssessmentFinalResult::where('fxr_id', '=', $string[1])->firstOrFail();
        $course_id = $checkCID->course_id;
        $submitted_by = $checkCID->submitted_by;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $string[0])
                 ->get();

        $assessment_result_list = DB::table('assessment_final_result')
                                ->select('assessment_final_result.*')
                                ->where('assessment_final_result.course_id','=',$course_id)
                                ->where('assessment_final_result.submitted_by','=',$checkCID->submitted_by)
                                ->where('assessment_final_result.student_id','=',$checkCID->student_id)
                                ->where('assessment_final_result.status','=','Active')
                                ->get();
        if(count($course)>0){
            return view('dean.FinalExamResult.viewWholePaper', compact('assessment_result_list','checkCID','submitted_by','string'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadFilesResult($fxr_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $string = explode('-',$fxr_id);

        $assessment_final_result = AssessmentFinalResult::where('fxr_id', '=', $string[1])->firstOrFail();
        $course_id = $assessment_final_result->course_id;
        $student_id = $assessment_final_result->student_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.lecturer', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $string[0])
                 ->get();

        if(count($course)>0){
            $ext = "";
            if($assessment_final_result->document!=""){
                $ext = explode(".", $assessment_final_result->document);
            }
            return Storage::disk('private')->download('Final_Result/'.$assessment_final_result->document, $assessment_final_result->document_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function checkCoursePerson($course_id)
    {
        $user_id       = auth()->user()->user_id;
        $checkid       = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $id            = $checkid->id;
        $faculty_id    = $checkid->faculty_id;
        $department_id = $checkid->department_id;

        if(auth()->user()->position=="Dean"){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*')
                    ->where('departments.faculty_id','=',$faculty_id)
                    ->get();
        }else if(auth()->user()->position=="HoD"){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*')
                    ->where('departments.department_id','=',$department_id)
                    ->get();
        }else if(auth()->user()->position=="Lecturer"){
            $course = DB::table('courses')
                 ->select('courses.*')
                 ->Where(function($query) use ($id){
                          $query->orWhere('courses.lecturer','=',$id)
                                ->orWhere('courses.moderator','=',$id);
                  })
                 ->where('course_id', '=', $course_id)
                 ->get();
        }
        
        if(count($course)>0){
            return true;
        }else{
            return false;
        }
    }
}
