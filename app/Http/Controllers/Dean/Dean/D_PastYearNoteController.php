<?php

namespace App\Http\Controllers\Dean\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Student;
use App\Staff;
use App\Lecture_Note;
use App\Course;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class D_PastYearNoteController extends Controller
{
	public function PastYearNote($id)
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
                    ->join('lecture_notes','courses.course_id','=','lecture_notes.course_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

        if(count($course)>0){
            return view('dean.Reviewer.PastYearNote.viewPYNote',compact('course','previous_semester'));
        }else{
            return redirect()->back();
        }
    }

    public function searchLecturerNote(Request $request)
    {
        $value         = $request->get('value');
        $semester      = $request->get('semester');
        $view_place    = $request->get('view_place');
        $course_id     = $request->get('course_id');
        
        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
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
                        ->join('courses','courses.course_id','=','lecture_notes.course_id')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('semesters','semesters.semester_id','=','courses.semester')
                        ->select('lecture_notes.*','courses.*','semesters.*','subjects.*')
                        ->Where(function($query) use ($value) {
                          $query->orWhere('lecture_notes.note_name','LIKE','%'.$value.'%');
                        })
                        ->where('subjects.subject_id', '=', $course[0]->subject_id)
                        ->where('lecture_notes.course_id', '!=', $course_id)
                        ->where('lecture_notes.status', '=', 'Active')
                        ->orderByDesc('semesters.semester_name')
                        ->orderBy('lecture_notes.note_place')
                        ->orderBy('lecture_notes.note_type')
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
                        $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/Reviewer/PastYearNote/'.$course_id.'/folder/'.$row->ln_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="assessment_word">';
                        if($row->used_by!=null){
                            foreach($all_note as $all_row){
                                if(($row->used_by)==($all_row->ln_id)){
                                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->semester_name." : ".$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                }
                            }
                        }else{
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->semester_name." : ".$data.$row->note_name.'</b></p>';
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
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/Reviewer/PastYear/lectureNote/download/'.$course_id."-".$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="pptx"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="assessment_word">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->semester_name." : ".$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->semester_name." : ".$data.$row->note_name.'</b></p>';
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
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/Reviewer/PastYear/images/lectureNote/'.$course_id.'-'.$row->ln_id.'/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.' '.$semester_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name_two">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.$semester_name.'</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
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
                $previous_semester = DB::table('courses')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                        ->join('staffs','staffs.id','=','courses.lecturer')
                        ->join('users','staffs.user_id','=','users.user_id')
                        ->join('lecture_notes','courses.course_id','=','lecture_notes.course_id')
                        ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                        ->where('subjects.subject_id', '=', $course[0]->subject_id)
                        ->where('courses.course_id','!=',$course_id)
                        ->where('courses.status', '=', 'Active')
                        ->orderByDesc('semesters.semester_name')
                        ->groupBy('courses.course_id')
                        ->get();
                if(count($previous_semester)>0) {
                    foreach($previous_semester as $row){
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-8 row align-self-center">';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_download">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/Reviewer/PastYearNote/'.$course_id.'/course/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
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
                    }
                }
            }
        return $result;
    }

    public function searchLecturerNotePrevious(Request $request)
    {
        $value         = $request->get('value');
        $semester      = $request->get('semester');
        $view_place    = $request->get('view_place');
        $course_id     = $request->get('course_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
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
                        ->join('courses','courses.course_id','=','lecture_notes.course_id')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('semesters','semesters.semester_id','=','courses.semester')
                        ->select('lecture_notes.*','courses.*','semesters.*','subjects.*')
                        ->Where(function($query) use ($value) {
                          $query->orWhere('lecture_notes.note_name','LIKE','%'.$value.'%');
                        })
                        ->where('subjects.subject_id', '=', $course[0]->subject_id)
                        ->where('lecture_notes.course_id', '=', $semester)
                        ->where('lecture_notes.status', '=', 'Active')
                        ->orderBy('lecture_notes.note_place')
                        ->orderBy('lecture_notes.note_type')
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
                        $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/Reviewer/PastYearNote/'.$course_id.'/folder/'.$row->ln_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="assessment_word">';
                        if($row->used_by!=null){
                            foreach($all_note as $all_row){
                                if(($row->used_by)==($all_row->ln_id)){
                                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                }
                            }
                        }else{
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$data.$row->note_name.'</b></p>';
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
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/Reviewer/PastYear/lectureNote/download/'.$course_id."-".$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="pptx"){
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
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$data.$row->note_name.'</b></p>';
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
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/Reviewer/PastYear/images/lectureNote/'.$course_id.'-'.$row->ln_id.'/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.' '.$semester_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name_two">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.$semester_name.'</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
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
                    ->where('course_id', '=', $semester)
                    ->where('note_place', '=', $view_place)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get(); 
            foreach($lecture_note as $row){
                if($row->note_type=="folder"){
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-12 row align-self-center">';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                        $result .= '</div>';
                        $result .= '<a href="'.$character.'/Reviewer/PastYearNote/'.$course_id.'/folder/'.$row->ln_id.'/" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="assessment_word">';
                        if($row->used_by!=null){
                            foreach($all_note as $all_row){
                                if(($row->used_by)==($all_row->ln_id)){
                                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                }
                            }
                        }else{
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->note_name.'</b></p>';
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
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/Reviewer/PastYear/lectureNote/download/'.$course_id."-".$row->ln_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }else if($ext[1]=="pptx"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="assessment_word">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->note_name.' <span style="color: grey;">( Used In : '.$all_row->semester_name.' )</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->note_name.'</b></p>';
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
                            $result .= '<input type="checkbox" value="'.$row->ln_id.'" class="group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/Reviewer/PastYear/images/lectureNote/'.$course_id.'-'.$row->ln_id.'/'.$row->note.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->note_name.' '.$semester_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name_two">';
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.$semester_name.'</span></b></p>';
                                    }
                                }
                            }else{
                                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->note_name.'</b></p>';
                            }
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
                }
            }
        }
        return $result;
    }

    public function PastYearNoteViewIn($id,$view,$view_id)
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

        $all_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','semesters.*','subjects.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('lecture_notes.status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();

        if($view=="course"){
        	$lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $view_id)
                    ->where('note_place', '=', 'Note')
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();      
            $previous_id = $view_id;
       	}else{
       		$ln = Lecture_Note::where('ln_id', '=', $view_id)->firstOrFail();
       		$place = $ln->note_place.',,,'.$ln->ln_id;
       		$lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $ln->course_id)
                    ->where('note_place', '=', $place)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();
            $previous_id = $ln->course_id;
            $place_name = explode(',,,',($ln['note_place']));
	        $i=1;
	        $data = "Note";
	        while(isset($place_name[$i])!=""){
	            $name = Lecture_Note::where('ln_id', '=', $place_name[$i])->firstOrFail();
	            $data .= ",,,".$name->note_name;
	            $i++;
	        }
        }

        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('courses.course_id', '=', $previous_id)
                 ->get();

        if(count($course)>0){
        	if($view=="course"){
            	return view('dean.Reviewer.PastYearNote.viewPYNoteFolder',compact('course','lecture_note','previous','all_note'));
        	}else{
        		return view('dean.Reviewer.PastYearNote.viewPYNoteFolder',compact('course','lecture_note','previous','ln','data','all_note'));
        	}
        }else{
            return redirect()->back();
        }
    }

    public function LectureNoteImage($ln_id,$image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $department_id = $staff_dean->department_id;
        $faculty_id    = $staff_dean->faculty_id;

        $id = explode('-',$ln_id);
        $checkCourseId = Lecture_Note::where('ln_id', '=', $id[1])->firstOrFail();

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
                     ->where('courses.course_id', '=', $id[0])
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
                     ->where('courses.course_id', '=', $id[0])
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        if(count($course)>0){
            $storagePath = storage_path('/private/Lecture_Note/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function downloadLN($id)
    {
    	$ids = explode('-',$id);
        $lecture_note = Lecture_Note::where('ln_id', '=', $ids[1])->firstOrFail();

        $user_id       = auth()->user()->user_id;
        $checkid       = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $checkid->faculty_id;
        $department_id = $checkid->department_id;

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
                     ->where('courses.course_id', '=', $ids[0])
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
                     ->where('courses.course_id', '=', $ids[0])
                     ->where('departments.department_id','=',$faculty_id)
                     ->get();
        }

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
}