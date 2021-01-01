<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use Auth;
use App\Student;
use App\Faculty;
use App\Programme;
use App\Lecture_Note;
use App\Semester;
use App\Subject;
use App\Course;
use ZipArchive;
use File;


class LectureNoteController extends Controller
{
	public function LectureNote($id)
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

        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('note_place', '=', 'Note')
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->orderBy('lecture_notes.used_by')
                    ->get();

        $all_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','semesters.*','subjects.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('lecture_notes.note_place', '=', 'Note')
                    ->where('lecture_notes.status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();

        if(count($course)>0){
            return view('student.Lecture_Note.Lecture_note',compact('course','lecture_note','all_note'));
        }else{
            return redirect()->back();
        }
	}

	public function searchLN(Request $request)
	{
		$value         = $request->get('value');
    	$place         = $request->get('place');
        $course_id     = $request->get('course_id');

        if(auth()->user()->position=="student"){
            $character = '/students';
        }
            
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $all_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','semesters.*','subjects.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('lecture_notes.status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();

	    $result = "";
	    if($value!=""){
	       	$lecture_note = DB::table('lecture_notes')
	                    ->select('lecture_notes.*')
                        ->Where(function($query) use ($value) {
                          $query->orWhere('note_name','LIKE','%'.$value.'%');
                        })
	                    ->where('course_id', '=', $course_id)
	                    ->where('status', '=', 'Active')
	                    ->orderByDesc('lecture_notes.note_type')
                        ->orderBy('lecture_notes.used_by')
	                    ->get();

	        if(count($lecture_note)>0) {
	        	foreach($lecture_note as $row){
                    $data = "";
                    if($row->note_place != "Note"){
                        $i=1;
                        $place = explode(',,,',$row->note_place);
                        $data = "";
                        while(isset($place[$i])!=""){
                            $name = Lecture_Note::where('ln_id', '=', $place[$i])->firstOrFail();
                            if($data==""){
                                $data .= $name->note_name." / " ;
                            }else{
                                $data .= $name->note_name." / ";
                            }
                            $i++;
                        }
                    }
		        	if($row->note_type=="folder"){
		            	$result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-12 row align-self-center">';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/lectureNote/folder/'.$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="assessment_word">';
                                if($row->used_by!=null){
                                  foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                      $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                    }
                                  }
                                }else{
                                  $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.'</b></p>';
                                }  
                        $result .= '</div>';
                        $result .= '</a>';
                        $result .= '</div>';
                        $result .= '</div>';
	                }else{
	            		$ext = "";
                        if($row->note){
                            $ext = explode(".", $row->note);
                        }
                        if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")||($ext[1] == "ppt")){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-12 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/lectureNote/download/'.$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="pptx"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="ppt"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="assessment_word">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '</div>'; 
                        }else{
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $semester_name = "<span style='color: grey;'> ( Used In : ".$all_row->semester_name.")</span>";
                                    }
                                }
                            }else{
                                $semester_name = '';
                            }
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/images/lectureNote/'.$row->ln_id.'/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.$semester_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.$semester_name.'</b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
	            	}
	            }
	        }else{
	        	$result .= '<div class="col-md-12">';
	            $result .= '<p>Not Found</p>';
	            $result .= '</div>';
	        }
	   	}else{
	        $lecture_note = DB::table('lecture_notes')
	                    ->select('lecture_notes.*')
	                    ->where('course_id', '=', $course_id)
	                    ->where('note_place', '=', $place)
	                    ->where('status', '=', 'Active')
	                    ->orderByDesc('lecture_notes.note_type')
                        ->orderBy('lecture_notes.used_by')
	                    ->get();
	        if(count($lecture_note)>0) {
	        	foreach($lecture_note as $row){
                    $data = "";
                    if($row->note_place != "Note"&&$row->note_place != $place){
                        $i=1;
                        $place = explode(',,,',$row->note_place);
                        $data = "";
                        while(isset($place[$i])!=""){
                            $name = Lecture_Note::where('ln_id', '=', $place[$i])->firstOrFail();
                            if($data==""){
                                $data .= $name->note_name." / " ;
                            }else{
                                $data .= $name->note_name." / ";
                            }
                            $i++;
                        }
                    }else{
                        $data = "";
                    }
	        		if($row->note_type=="folder"){
                          $result .= '<div class="col-12 row align-self-center" id="course_list">';
                          $result .= '<div class="col-9 row align-self-center">';
                          $result .= '<div class="checkbox_style align-self-center">';
                          $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                          $result .= '</div>';
                          $result .= '<a href="'.$character.'/lectureNote/folder/'.$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                          $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                          $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                          $result .= '</div>';
                          $result .= '<div class="col-10" id="course_name">';
                                if($row->used_by!=null){
                                  foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                      $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                    }
                                  }
                                }else{
                                  $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.'</b></p>';
                                }  
                          $result .= '</div>';
                          $result .= '</a>';
                          $result .= '</div>';
                          $result .= '</div>';
	            	}else{
	            		$ext = "";
                        if($row->note){
                            $ext = explode(".", $row->note);
                        }
                        if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")||($ext[1] == "ppt")){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/lectureNote/download/'.$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="pptx"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="ppt"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }else{
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $semester_name = "<span style='color: grey;'> ( Used In : ".$all_row->semester_name.")</span>";
                                    }
                                }
                            }else{
                                $semester_name = '';
                            }
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/images/lectureNote/'.$row->ln_id.'/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.' '.$semester_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.$semester_name.'</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
	            	}
	        	}
	       	}else{
	       		$result .= '<div class="col-md-12">';
	            $result .= '<p>Not Found</p>';
	            $result .= '</div>';
	       	}
	    }
	    return $result;
	}

	public function LNFolderView($folder_id)
	{
		$user_id      = auth()->user()->user_id;
        $student      = Student::where('user_id', '=', $user_id)->firstOrFail();

        $lecture_note = Lecture_Note::where('ln_id', '=', $folder_id)->firstOrFail();

        $course    = DB::table('assign_student_course')
                    ->join('courses', 'courses.course_id', '=', 'assign_student_course.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('assign_student_course.*','semesters.*','subjects.*','staffs.*','users.*')
                    ->where('assign_student_course.student_id', '=', $student->student_id)
                    ->where('assign_student_course.course_id','=',$lecture_note->course_id)
                    ->get();
        
        $place_name = explode(',,,',($lecture_note->note_place));
        $i=1;
        $data = "Note";
        while(isset($place_name[$i])!=""){
            $name = Lecture_Note::where('ln_id', '=', $place_name[$i])->firstOrFail();

            $data .= ",,,".$name->note_name;
            $i++;
        }

        $note_place = $lecture_note->note_place.",,,".$lecture_note->ln_id;

        $lecture_note_list = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $lecture_note->course_id)
                    ->where('note_place', '=', $note_place)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->orderBy('used_by')
                    ->get();

         $all_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','semesters.*','subjects.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('lecture_notes.status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();

        if(count($course)>0){
        	return view('student.Lecture_Note.LNFolderView', compact('course','note_place','lecture_note','lecture_note_list','all_note','data'));
        }else{
            return redirect()->back();
        }
	}

    public function LectureNoteImage($ln_id,$image_name)
    {
        $user_id    = auth()->user()->user_id;
        $student    = Student::where('user_id', '=', $user_id)->firstOrFail();

        $checkCourseId = Lecture_Note::where('ln_id', '=', $ln_id)->firstOrFail();
        $course_id = $checkCourseId->course_id;

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
            $storagePath = storage_path('/private/Lecture_Note/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function downloadLN($id)
    {
        $lecture_note = Lecture_Note::where('ln_id', '=', $id)->firstOrFail();
        $course_id = $lecture_note->course_id;

        $user_id    = auth()->user()->user_id;
        $student    = Student::where('user_id', '=', $user_id)->firstOrFail();

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
            $ext = "";
            if($lecture_note->note!=""){
                $ext = explode(".", $lecture_note->note);
            }
            return Storage::disk('private')->download('Lecture_Note/'.$lecture_note->note, $lecture_note->note_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function zipFileDownload($course_id,$download){
        if($download == "All"){
            $f_course_id = $course_id;
        }else if($download== "folder"){
            $f_ln_id = $course_id;
            $note = Lecture_Note::where('ln_id', '=', $f_ln_id)->firstOrFail();
            $f_course_id = $note->course_id;
        }else{
            $string = explode('---',$course_id);
            $f_course_id = $string[0];
        }

        $subjects = DB::table('subjects')
                    ->join('courses','courses.subject_id','=','subjects.subject_id')
                    ->join('semesters','courses.semester','=','semesters.semester_id')
                    ->select('courses.*','subjects.*','semesters.*')
                    ->where('courses.course_id', '=', $f_course_id)
                    ->get();

        $all_note = DB::table('lecture_notes')
                        ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                        ->select('lecture_notes.*','courses.*','semesters.*','subjects.*')
                        ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                        ->where('lecture_notes.status', '=', 'Active')
                        ->get();

        $ZipFile_name = $subjects[0]->subject_code." ".$subjects[0]->subject_name;
        $zip = new ZipArchive;
        $fileName = storage_path('private/Lecture_Note/Zip_Files/'.$ZipFile_name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Lecture_Note/'));

        if($download == "All"){
            $lecture_note = DB::table('lecture_notes')
                        ->join('courses','courses.course_id','=','lecture_notes.course_id')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('semesters','semesters.semester_id','=','courses.semester')
                        ->select('lecture_notes.*','courses.*','semesters.*','subjects.*')
                        ->where('subjects.subject_id', '=', $subjects[0]->subject_id)
                        ->where('lecture_notes.course_id', '=', $f_course_id)
                        ->where('lecture_notes.status', '=', 'Active')
                        ->orderByDesc('lecture_notes.note_type')
                        ->get();
            foreach($lecture_note as $row){
                if($row->note_type == "document"){
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($row->note==$relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($row->note_place=="Note"){
                                if($row->used_by!=null){
                                    foreach($all_note as $all_row){
                                        if($row->used_by==$all_row->ln_id){
                                            $zip->addFile($value,$row->note_name." (".$all_row->semester_name.")".'.'.$ext[1]);
                                        }
                                    }
                                }else{
                                    $zip->addFile($value,$row->note_name.'.'.$ext[1]);
                                }
                            }else{
                                $i=1;
                                $place = explode(',,,',$row->note_place);
                                $data = "";
                                while(isset($place[$i])!=""){
                                    $name = Lecture_Note::where('ln_id', '=', $place[$i])->firstOrFail();
                                    $semester = "";
                                    $used_by = $name->used_by;
                                    foreach($all_note as $all_row){
                                        if($used_by==$all_row->ln_id){
                                            $semester = $all_row->semester_name;
                                        }
                                    }
                                    if($data==""){
                                        if($used_by!=null){
                                            $data .= $name->note_name." (".$semester.")";
                                        }else{
                                            $data .= $name->note_name;
                                        }
                                    }else{

                                        if($used_by!=null){
                                            $data .= "/".$name->note_name." (".$semester.")";
                                        }else{
                                            $data .= "/".$name->note_name;
                                        }
                                    }
                                    $i++;
                                }
                                if($row->used_by!=null){
                                    $zip->addFile($value,$data.'/'.$row->note_name." (".$semester.")".'.'.$ext[1]);
                                }else{
                                    $zip->addFile($value,$data.'/'.$row->note_name.'.'.$ext[1]);
                                }
                            }
                        }
                    }
                }else{
                    if($row->note_place=="Note"){
                        if($row->used_by!=null){
                            foreach($all_note as $all_row){
                                if($row->used_by==$all_row->ln_id){
                                    $zip->addEmptyDir($row->note_name." (".$all_row->semester_name.")");
                                }
                            }
                        }else{
                            $zip->addEmptyDir($row->note_name);
                        }
                    }else{
                        $i=1;
                        $place = explode(',,,',$row->note_place);
                        $data = "";
                        while(isset($place[$i])!=""){
                            $name = Lecture_Note::where('ln_id', '=', $place[$i])->firstOrFail();
                            $semester = "";
                            $used_by = $name->used_by;
                            foreach($all_note as $all_row){
                                if($used_by==$all_row->ln_id){
                                    $semester = $all_row->semester_name;
                                }
                            }
                            if($data==""){
                                if($used_by!=null){
                                    $data .= $name->note_name." (".$semester.")";
                                }else{
                                    $data .= $name->note_name;
                                }
                            }else{

                                if($used_by!=null){
                                    $data .= "/".$name->note_name." (".$semester.")";
                                }else{
                                    $data .= "/".$name->note_name;
                                }
                            }
                            $i++;
                        }
                        if($row->used_by!=null){
                            $zip->addEmptyDir($data.'/'.$row->note_name." (".$semester.")");
                        }else{
                            $zip->addEmptyDir($data.'/'.$row->note_name);
                        }
                    }
                }
            }
        }else if($download == "checked"){
            for($i=1;$i<(count($string)-1);$i++){

                $note = Lecture_Note::where('ln_id', '=', $string[$i])->firstOrFail();

                if($note->note_type == "document"){
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($note->note==$relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($note->note_place=="Note"){
                                if($note->used_by!=null){
                                    foreach($all_note as $all_row){
                                        if($note->used_by==$all_row->ln_id){
                                            $zip->addFile($value,$note->note_name." (".$all_row->semester_name.")".'.'.$ext[1]);
                                        }
                                    }
                                }else{
                                    $zip->addFile($value,$note->note_name.'.'.$ext[1]);
                                }
                            }else{
                                $m=1;
                                $place = explode(',,,',$note->note_place);
                                $data = "";
                                while(isset($place[$m])!=""){
                                    $name = Lecture_Note::where('ln_id', '=', $place[$m])->firstOrFail();
                                    $semester = "";
                                    $used_by = $name->used_by;
                                    foreach($all_note as $all_row){
                                        if($used_by==$all_row->ln_id){
                                            $semester = $all_row->semester_name;
                                        }
                                    }
                                    if($data==""){
                                        if($used_by!=null){
                                            $data .= $name->note_name." (".$semester.")";
                                        }else{
                                            $data .= $name->note_name;
                                        }
                                    }else{

                                        if($used_by!=null){
                                            $data .= "/".$name->note_name." (".$semester.")";
                                        }else{
                                            $data .= "/".$name->note_name;
                                        }
                                    }
                                    $m++;
                                }
                                if($note->used_by!=null){
                                    $zip->addFile($value,$data.'/'.$note->note_name." (".$semester.")".'.'.$ext[1]);
                                }else{
                                    $zip->addFile($value,$data.'/'.$note->note_name.'.'.$ext[1]);
                                }
                            }
                        }
                    }
                }else{
                    $check = $note->note_place.",,,".$note->ln_id;
                    $next_check = $note->note_place.",,,".$note->ln_id.",,,";
                    $lecture_note = DB::table('lecture_notes')
                                    ->select('lecture_notes.*')
                                    ->Where(function($query) use ($check,$next_check) {
                                      $query->orWhere('note_place','LIKE','%'.$check)
                                            ->orWhere('note_place','LIKE','%'.$next_check.'%');
                                    })
                                    ->where('lecture_notes.status', '=', 'Active')
                                    ->orderByDesc('lecture_notes.note_type')
                                    ->get();
                    foreach($lecture_note as $row){
                        if($row->note_type == "document"){
                            foreach ($files as $key => $value) {
                                $relativeNameInZipFile = basename($value);
                                if($row->note==$relativeNameInZipFile){
                                    $ext = explode('.',$relativeNameInZipFile);
                                    if($row->note_place=="Note"){
                                        if($row->used_by!=null){
                                            foreach($all_note as $all_row){
                                                if($row->used_by==$all_row->ln_id){
                                                    $zip->addFile($value,$row->note_name." (".$all_row->semester_name.")".'.'.$ext[1]);
                                                }
                                            }
                                        }else{
                                            $zip->addFile($value,$row->note_name.'.'.$ext[1]);
                                        }
                                    }else{
                                        $m=1;
                                        $place = explode(',,,',$row->note_place);
                                        $data = "";
                                        while(isset($place[$m])!=""){
                                            $name = Lecture_Note::where('ln_id', '=', $place[$m])->firstOrFail();
                                            $semester = "";
                                            $used_by = $name->used_by;
                                            foreach($all_note as $all_row){
                                                if($used_by==$all_row->ln_id){
                                                    $semester = $all_row->semester_name;
                                                }
                                            }
                                            if($data==""){
                                                if($used_by!=null){
                                                    $data .= $name->note_name." (".$semester.")";
                                                }else{
                                                    $data .= $name->note_name;
                                                }
                                            }else{

                                                if($used_by!=null){
                                                    $data .= "/".$name->note_name." (".$semester.")";
                                                }else{
                                                    $data .= "/".$name->note_name;
                                                }
                                            }
                                            $m++;
                                        }
                                        if($row->used_by!=null){
                                            $zip->addFile($value,$data.'/'.$row->note_name." (".$semester.")".'.'.$ext[1]);
                                        }else{
                                            $zip->addFile($value,$data.'/'.$row->note_name.'.'.$ext[1]);
                                        }
                                    }
                                }
                            }
                        }else{
                            if($row->note_place=="Note"){
                                if($row->used_by!=null){
                                    foreach($all_note as $all_row){
                                        if($row->used_by==$all_row->ln_id){
                                            $zip->addEmptyDir($row->note_name." (".$all_row->semester_name.")");
                                        }
                                    }
                                }else{
                                    $zip->addEmptyDir($row->note_name);
                                }
                            }else{
                                $m=1;
                                $place = explode(',,,',$row->note_place);
                                $data = "";
                                while(isset($place[$m])!=""){
                                    $name = Lecture_Note::where('ln_id', '=', $place[$m])->firstOrFail();
                                    $semester = "";
                                    $used_by = $name->used_by;
                                    foreach($all_note as $all_row){
                                        if($used_by==$all_row->ln_id){
                                            $semester = $all_row->semester_name;
                                        }
                                    }
                                    if($data==""){
                                        if($used_by!=null){
                                            $data .= $name->note_name." (".$semester.")";
                                        }else{
                                            $data .= $name->note_name;
                                        }
                                    }else{

                                        if($used_by!=null){
                                            $data .= "/".$name->note_name." (".$semester.")";
                                        }else{
                                            $data .= "/".$name->note_name;
                                        }
                                    }
                                    $m++;
                                }
                                if($row->used_by!=null){
                                    $zip->addEmptyDir($data.'/'.$row->note_name." (".$semester.")");
                                }else{
                                    $zip->addEmptyDir($data.'/'.$row->note_name);
                                }
                            }
                        }
                    }
                }
            }
        }else if($download == "folder"){
            if($note->note_type == "document"){
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($note->note==$relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($note->note_place=="Note"){
                            if($note->used_by!=null){
                                foreach($all_note as $all_row){
                                    if($note->used_by==$all_row->ln_id){
                                        $zip->addFile($value,$note->note_name." (".$all_row->semester_name.")".'.'.$ext[1]);
                                    }
                                }
                            }else{
                                $zip->addFile($value,$note->note_name.'.'.$ext[1]);
                            }
                        }else{
                            $m=1;
                            $place = explode(',,,',$note->note_place);
                            $data = "";
                            while(isset($place[$m])!=""){
                                $name = Lecture_Note::where('ln_id', '=', $place[$m])->firstOrFail();
                                $semester = "";
                                $used_by = $name->used_by;
                                foreach($all_note as $all_row){
                                    if($used_by==$all_row->ln_id){
                                        $semester = $all_row->semester_name;
                                    }
                                }
                                if($data==""){
                                    if($used_by!=null){
                                        $data .= $name->note_name." (".$semester.")";
                                    }else{
                                        $data .= $name->note_name;
                                    }
                                }else{
                                    if($used_by!=null){
                                        $data .= "/".$name->note_name." (".$semester.")";
                                    }else{
                                        $data .= "/".$name->note_name;
                                    }
                                }
                                $m++;
                            }
                            if($row->used_by!=null){
                                $zip->addFile($value,$data.'/'.$note->note_name." (".$semester.")".'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$data.'/'.$note->note_name.'.'.$ext[1]);
                            }
                        }
                    }
                }
            }else{
                $check = $note->note_place.",,,".$note->ln_id;
                $lecture_note = DB::table('lecture_notes')
                                    ->select('lecture_notes.*')
                                    ->where('note_place','LIKE','%'.$check.'%')
                                    ->where('lecture_notes.status', '=', 'Active')
                                    ->orderByDesc('lecture_notes.note_type')
                                    ->get();
                foreach($lecture_note as $row){
                    if($row->note_type == "document"){
                        foreach ($files as $key => $value) {
                            $relativeNameInZipFile = basename($value);
                            if($row->note==$relativeNameInZipFile){
                                $ext = explode('.',$relativeNameInZipFile);
                                if($row->note_place=="Note"){
                                    if($row->used_by!=null){
                                        foreach($all_note as $all_row){
                                            if($row->used_by==$all_row->ln_id){
                                                $zip->addFile($value,$row->note_name." (".$all_row->semester_name.")".'.'.$ext[1]);
                                            }
                                        }
                                    }else{
                                        $zip->addFile($value,$row->note_name.'.'.$ext[1]);
                                    }
                                }else{
                                    $m=1;
                                    $place = explode(',,,',$row->note_place);
                                    $data = "";
                                    while(isset($place[$m])!=""){
                                        $name = Lecture_Note::where('ln_id', '=', $place[$m])->firstOrFail();
                                        $semester = "";
                                        $used_by = $name->used_by;
                                        foreach($all_note as $all_row){
                                            if($used_by==$all_row->ln_id){
                                                $semester = $all_row->semester_name;
                                            }
                                        }
                                        if($data==""){
                                            if($used_by!=null){
                                                $data .= $name->note_name." (".$semester.")";
                                            }else{
                                                $data .= $name->note_name;
                                            }
                                        }else{
                                            if($used_by!=null){
                                                $data .= "/".$name->note_name." (".$semester.")";
                                            }else{
                                                $data .= "/".$name->note_name;
                                            }
                                        }
                                        $m++;
                                    }
                                    if($row->used_by!=null){
                                        $zip->addFile($value,$data.'/'.$row->note_name." (".$semester.")".'.'.$ext[1]);
                                    }else{
                                        $zip->addFile($value,$data.'/'.$row->note_name.'.'.$ext[1]);
                                    }
                                }
                            }
                        }
                    }else{
                        if($row->note_place=="Note"){
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if($row->used_by==$all_row->ln_id){
                                        $zip->addEmptyDir($row->note_name." (".$all_row->semester_name.")");
                                    }
                                }
                            }else{
                                $zip->addEmptyDir($row->note_name);
                            }
                        }else{
                            $m=1;
                            $place = explode(',,,',$row->note_place);
                            $data = "";
                            while(isset($place[$m])!=""){
                                $name = Lecture_Note::where('ln_id', '=', $place[$m])->firstOrFail();
                                $semester = "";
                                $used_by = $name->used_by;
                                foreach($all_note as $all_row){
                                    if($used_by==$all_row->ln_id){
                                        $semester = $all_row->semester_name;
                                    }
                                }
                                if($data==""){
                                    if($used_by!=null){
                                        $data .= $name->note_name." (".$semester.")";
                                    }else{
                                        $data .= $name->note_name;
                                    }
                                }else{

                                    if($used_by!=null){
                                        $data .= "/".$name->note_name." (".$semester.")";
                                    }else{
                                        $data .= "/".$name->note_name;
                                    }
                                }
                                $m++;
                            }
                            if($row->used_by!=null){
                                $zip->addEmptyDir($data.'/'.$row->note_name." (".$semester.")");
                            }else{
                                $zip->addEmptyDir($data.'/'.$row->note_name);
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
            Storage::disk('private')->delete('/Lecture_Note/Zip_Files/'.$ZipFile_name.'.zip');
            return redirect()->back();
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
