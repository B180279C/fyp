<?php

namespace App\Http\Controllers\Dean\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Staff;
use App\Faculty;
use App\Programme;
use App\Lecture_Note;
use App\Semester;
use App\Subject;
use App\Course;


class D_LectureNoteController extends Controller
{
	public function DeanLectureNote($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

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
            return view('dean.Dean.Lecture_Note.D_Lecture_note',compact('course','lecture_note','all_note'));
        }else{
            return redirect()->back();
        }
	}

	public function searchDeanLN(Request $request)
	{
		$value         = $request->get('value');
    	$place         = $request->get('place');
        $course_id     = $request->get('course_id');
            
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
                        $result .= '<a href="/Dean/lectureNote/folder/'.$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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
                        if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-12 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="'.action('Dean\LectureNoteController@downloadLN',$row->ln_id).'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="pptx"){
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
                            $result .= '<a href="/images/lectureNote/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.$semester_name.'">';
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
                          $result .= '<a href="/Dean/lectureNote/folder/'.$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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
                        if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="'.action('Dean\LectureNoteController@downloadLN',$row->ln_id).'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="pptx"){
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
                                        $semester_name = '<span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span>';
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
                            $result .= '<a href="/images/lectureNote/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.' '.$semester_name.'">';
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

	public function DeanLNFolderView($folder_id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $lecture_note = Lecture_Note::where('ln_id', '=', $folder_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $lecture_note->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
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
        	return view('dean.Dean.Lecture_Note.D_LNFolderView', compact('course','note_place','lecture_note','lecture_note_list','all_note','data'));
        }else{
            return redirect()->back();
        }
	}
}
