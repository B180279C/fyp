<?php

namespace App\Http\Controllers\Student;

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
    	$user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$id)
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

        if(count($course)>0){
            return view('student.PastYear.viewAssessmentFinal',compact('course','previous_semester'));
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

        if(auth()->user()->position=="student"){
            $character = '/students';
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
    // 	$user_id   = auth()->user()->user_id;
    //     $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

    //     $course    = DB::table('assign_student_course')
    //                 ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
    //                 ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
    //                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //                 ->join('staffs', 'staffs.id','=','courses.lecturer')
    //                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
    //                 ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
    //                 ->where('assign_student_course.student_id', '=', $student->student_id)
    //                 ->where('assign_student_course.course_id','=',$id)
    //                 ->get();

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
    //         return view('student.PastYear.viewFinalAssessmentName',compact('course','previous','ass_final','id'));
    //     }else{
    //         return redirect()->back();
    //     }
    // }

    // public function searchAssessmentName(Request $request)
    // {
    // 	$value         = $request->get('value');
    //     $course_id     = $request->get('course_id');
    //     $original_id   = $request->get('original_id');

    //     if(auth()->user()->position=="student"){
    //         $character = '/students';
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

    // public function zipFileDownloadName($course_id,$download)
    // {
    // 	if($download == "All"){
    //         $string = explode('---',$course_id);
    //         $original_id = $string[0];
    //         $f_course_id = $string[1];
    //     }else{
    //         $string = explode('---',$course_id);
    //         $original_id = $string[0];
    //         $f_course_id = $string[1];
    //     }

    //     $subjects = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('course_id', '=', $f_course_id)
    //              ->get();

    //     $ZipFile_name = $subjects[0]->subject_code." ".$subjects[0]->subject_name." ( ".$subjects[0]->semester_name." )";
    //     $zip = new ZipArchive;
    //     $fileName = storage_path('private/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
    //     $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    //     $files = File::files(storage_path('/private/Assessment_Final/'));

    //     if($download == "checked"){
    //         for($i=2;$i<(count($string)-1);$i++){

    //             $AssFinal = AssFinal::where('fx_id','=',$string[$i])->firstOrFail();
    //             $course_id = $AssFinal->course_id;

    //             $zip->addEmptyDir($AssFinal->assessment_name);
    //             $result_list = DB::table('assessment_final')
    //                      ->select('assessment_final.*')
    //                      ->where('assessment_final.fx_id', '=', $AssFinal->fx_id)
    //                      ->where('assessment_final.status','=','Active')
    //                      ->get();
    //             foreach($result_list as $rl_row){
    //                 if($rl_row->ass_fx_type=="Question"){
    //                     $zip->addEmptyDir($AssFinal->assessment_name."/Question");
    //                 }else{
    //                     $zip->addEmptyDir($AssFinal->assessment_name."/Solution"); 
    //                 }
    //                 foreach ($files as $key => $value) {
    //                     $relativeNameInZipFile = basename($value);
    //                     if($rl_row->ass_fx_document == $relativeNameInZipFile){
    //                         $ext = explode('.',$relativeNameInZipFile);
    //                         if($rl_row->ass_fx_type=="Question"){
    //                             $zip->addFile($value,$AssFinal->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
    //                         }else{
    //                             $zip->addFile($value,$AssFinal->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
    //                         } 
    //                     }
    //                 }
    //             }
    //         }
    //     }else if($download == "searchedWord"){
    //         for($i=2;$i<(count($string)-1);$i++){

    //             $assessment_final = AssessmentFinal::where('ass_fx_id','=',$string[$i])->firstOrFail();
    //             $fx_id = $assessment_final->fx_id;

    //             $AssFinal = AssFinal::where('fx_id','=',$fx_id)->firstOrFail();
    //             $course_id = $AssFinal->course_id;

    //             $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('course_id', '=', $course_id)
    //              ->get();

    //              $zip->addEmptyDir($AssFinal->assessment_name);

    //              if($assessment_final->ass_fx_type=="Question"){
    //                 $zip->addEmptyDir($AssFinal->assessment_name."/Question");
    //              }else{
    //                 $zip->addEmptyDir($AssFinal->assessment_name."/Solution"); 
    //              }
    //              foreach ($files as $key => $value) {
    //                 $relativeNameInZipFile = basename($value);
    //                 if($assessment_final->ass_fx_document == $relativeNameInZipFile){
    //                     $ext = explode('.',$relativeNameInZipFile);
    //                     if($assessment_final->ass_fx_type=="Question"){
    //                         $zip->addFile($value,$AssFinal->assessment_name."/Question/".$assessment_final->ass_fx_name.'.'.$ext[1]);
    //                     }else{
    //                         $zip->addFile($value,$AssFinal->assessment_name."/Solution/".$assessment_final->ass_fx_name.'.'.$ext[1]);
    //                     } 
    //                 }
    //             }
    //         }
    //     }else{
    //         $ass_final = DB::table('ass_final')
    //                          ->select('ass_final.*')
    //                          ->where('course_id', '=', $string[1])
    //                          ->where('status', '=', 'Active')
    //                          ->get();

    //         foreach($ass_final as $ass_row){
    //                     $zip->addEmptyDir($ass_row->assessment_name);
    //                     $result_list = DB::table('assessment_final')
    //                          ->select('assessment_final.*')
    //                          ->where('assessment_final.fx_id', '=', $ass_row->fx_id)
    //                          ->where('assessment_final.status','=','Active')
    //                          ->get();
    //             foreach($result_list as $rl_row){
    //                         if($rl_row->ass_fx_type=="Question"){
    //                             $zip->addEmptyDir($ass_row->assessment_name."/Question");
    //                         }else{
    //                             $zip->addEmptyDir($ass_row->assessment_name."/Solution"); 
    //                         }
    //                 foreach ($files as $key => $value) {
    //                     $relativeNameInZipFile = basename($value);
    //                     if($rl_row->ass_fx_document == $relativeNameInZipFile){
    //                         $ext = explode('.',$relativeNameInZipFile);
    //                         if($rl_row->ass_fx_type=="Question"){
    //                             $zip->addFile($value,$ass_row->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
    //                         }else{
    //                             $zip->addFile($value,$ass_row->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
    //                         } 
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     $zip->close();
    //     if($this->checkCoursePerson($original_id)==true){
    //         return response()->download($fileName)->deleteFileAfterSend(true);
    //     }else{
    //         Storage::disk('private')->delete('/Assessment_Final/PastYear/'.$ZipFile_name.'.zip');
    //         return redirect()->back();
    //     }
    // }

    public function PastYearAssessmentList($id,$course_id)
    {
    	$user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$id)
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
            return view('student.PastYear.viewFinalAssessmentList',compact('course','AssFinal','previous','assessment_list','group_list'));
        }else{
            return redirect()->back();
        }
    }

    public function searchAssessmentlist(Request $request)
    {
    	$value         = $request->get('value');
        $fx_id         = $request->get('fx_id');
        $original_id   = $request->get('original_id');

        if(auth()->user()->position=="student"){
            $character = '/students';
        }

        $AssFinal = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();

        $user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

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

    public function FinalAssessmentImage($image_name)
    {
        $user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

        $string = explode('-',$image_name);

        $checkImageFXID = AssessmentFinal::where('ass_fx_document', '=', $string[1])->firstOrFail();
        $fx_id = $checkImageFXID->fx_id;

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$string[0])
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
        $user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

        $string = explode('-',$fx_id);
        $assessment_final = AssFinal::where('course_id', '=', $string[1])->firstOrFail();

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$string[0])
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
            return view('student.FinalExam.viewWholePaper', compact('assessment_list','assessment_final','string'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadFiles($ass_fx_id)
    {
        $user_id   = auth()->user()->user_id;
        $student   = Student::where('user_id', '=', $user_id)->firstOrFail();

        $string = explode('-',$ass_fx_id);
        $assessment_final = AssessmentFinal::where('ass_fx_id', '=', $string[1])->firstOrFail();
        $course_id = $assessment_final->course_id;

        // $AssFinal = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        // $question = $AssFinal->assessment_name;

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$string[0])
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

    public function checkCoursePerson($course_id)
    {
        $user_id       = auth()->user()->user_id;
        $student       = Student::where('user_id', '=', $user_id)->firstOrFail();

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->get();
        
        if(count($course)>0){
            return true;
        }else{
            return false;
        }
    }
}
