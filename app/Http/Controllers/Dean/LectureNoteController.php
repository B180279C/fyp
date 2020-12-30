<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\Semester;
use App\Lecture_Note;
use App\Course;
use ZipArchive;
use File;

class LectureNoteController extends Controller
{
    public function viewLectureNote($id){
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
            return view('dean.LectureNote.viewLectureNote',compact('course','lecture_note','previous_semester','all_note'));
        }else{
            return redirect()->back();
        }
    }

    public function openNewFolder(Request $request){
    	$course_id	  = $request->get('course_id');
        $folder_name  = $request->get('folder_name');
        $type         = "folder";
        $place        = $request->get('folder_place');

        $lecture_note = new lecture_Note([
            'course_id'         =>  $course_id,
            'note_name'         =>  $folder_name,
            'note_type'         =>  $type,
            'note_place'        =>  $place,
            'status'            =>  'Active',
        ]);
        $lecture_note->save();

        return redirect()->back()->with('success','New Folder Added Successfully');
    }

    public function folderNameEdit(Request $request){
        $folder_id = $request->get('value');
        $folder = lecture_Note::find($folder_id);
        return $folder;
    }

    public function SelectPreviousSemester(Request $request){
        $course_id = $request->get('value');

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

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
        return $previous_semester;
    }

    public function SelectFolderSemester(Request $request){
        $course_id = $request->get('value');

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $lecture_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','courses.*','semesters.*')
                    ->where('lecture_notes.course_id', '=', $course_id)
                    ->where('lecture_notes.note_place', '=', 'Note')
                    ->where('lecture_notes.status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();

        $all_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','courses.*','semesters.*','subjects.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('lecture_notes.note_place', '=', 'Note')
                    ->where('lecture_notes.status', '=', 'Active')
                    ->get();

        return response()->json([$lecture_note,$all_note]);
    }

    public function SelectFolderPlace(Request $request){
        $ln_id = $request->get('value');
        $lecture_note = lecture_Note::where('ln_id', '=', $ln_id)->firstOrFail();
        $place = $lecture_note->note_place.",,,".$lecture_note->ln_id;

        $course_id = $lecture_note->course_id;

        $course = Course::where('course_id', '=', $course_id)->firstOrFail();

        $semester = Semester::where('semester_id', '=', $course->semester)->firstOrFail();
        $semester_name = $semester->semester_name;

        if($lecture_note->note_place=="Note"){
            $data = $lecture_note->note_name; 
        }else{
            $place_name = explode(',,,',($lecture_note->note_place));
            $i=1;
            $data = "";
            while(isset($place_name[$i])!=""){
                $name = Lecture_Note::where('ln_id', '=', $place_name[$i])->firstOrFail();
                $data .= $name->note_name.",,,";
                $i++;
            } 
            $data .= $lecture_note->note_name;
        }
        return $course_id."___".$semester_name."___".$ln_id."___".$place."___".$data;
    }

    public function SelectFolder(Request $request){
        $place = $request->get('value');

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $request->get('course_id'))
                 ->get();

        $lecture_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','courses.*','semesters.*')
                    ->where('lecture_notes.note_place', '=', $place)
                    ->where('lecture_notes.status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();

        $all_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','courses.*','semesters.*','subjects.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('lecture_notes.note_place', '!=', 'Note')
                    ->where('lecture_notes.status', '=', 'Active')
                    ->get();

        return response()->json([$lecture_note,$all_note]);
    }

    public function GetUsedSemester(Request $request)
    {
        $ln_id = $request->get('value');
        $lecture_note = DB::table('lecture_notes')
                    ->join('courses', 'courses.course_id', '=', 'lecture_notes.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('lecture_notes.*','courses.*','semesters.*')
                    ->where('lecture_notes.ln_id', '=', $ln_id)
                    ->where('lecture_notes.status', '=', 'Active')
                    ->get();
        return $lecture_note[0]->semester_name;
    }

    public function updateFolderName(Request $request){
        $ln_id   = $request->get('ln_id');
        $lecture_note = lecture_Note::where('ln_id', '=', $ln_id)->firstOrFail();
        $lecture_note->note_name  = $request->get('folder_name');
        $lecture_note->save();
        return redirect()->back()->with('success','Edit Folder Name Successfully');
    }

    public function removeActive($id){
        $lecture_note = lecture_Note::where('ln_id', '=', $id)->firstOrFail();
        if($lecture_note->note_type=="folder"){
            $lecture_note_list = lecture_Note::where('note_place', 'LIKE', $lecture_note->note_place.",,,".$id)->update(['status' => 'Remove']);
            $lecture_note_list_2 = lecture_Note::where('note_place', 'LIKE', $lecture_note->note_place.",,,".$id.",,,".'%')->update(['status' => 'Remove']);
        }
        $lecture_note->status  = "Remove";
        $lecture_note->save();
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/lecture_note/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('fake/lecture_note/'.$filename);
        return $filename;  
    }

    public function storeFiles(Request $request){

        $count = $request->get('count');
        $place = $request->get('file_place');
        for($i=1;$i<=$count;$i++){
            $name = $request->get('form'.$i);
            $ext = $request->get('ext'.$i);
            $fake = $request->get('fake'.$i);
            if($name!=""){
                $lecture_note = new Lecture_Note([
                    'course_id'              =>  $request->get('course_id'),
                    'note_name'              =>  $name,
                    'note_type'              =>  'document',
                    'note_place'             =>  $place,
                    'note'                   =>  $fake,
                    'status'                 =>  'Active',
                ]);
                $lecture_note->save();
                $fake_place = Storage::disk('private')->get("fake/lecture_note/".$fake);
                Storage::disk('private')->put('Lecture_Note/'.$fake, $fake_place); 
                Storage::disk('private')->delete("fake/lecture_note/".$fake);
            }
        }
        return redirect()->back()->with('success','New Document Added Successfully');
    }

    public function storePreviousFiles(Request $request){
        $c_count = $request->get('c_count');
        $course_id = $request->get('course_id');
        $checkbox_input = $request->get('checkbox_input');
        $array = array();
        $array_folder = array();
        $i=1;
        $lnID = explode('---',($checkbox_input));
        while(isset($lnID[$i])!=""){
            $ln = Lecture_Note::where('ln_id', '=', $lnID[$i])->firstOrFail();
            $place = $ln->note_place;
            if($place!="Note"){    
                $place_id = explode(',,,',$place);
                for($m=1;$m<=(count($place_id))-1;$m++){
                    array_push($array,$place_id[$m]);
                }   
            }
            $i++;
        }
        $unique = array_unique($array);
        $unique = array_values($unique);
        for($n = 0; $n<(count($unique));$n++){
            $folder_ln = Lecture_Note::where('ln_id', '=', $unique[$n])->firstOrFail();
            $place = $folder_ln->note_place;
            $used_by = $folder_ln->used_by;
            if($used_by==null){
                $used_by = $folder_ln->ln_id;
            }
            if($place=="Note"){
                $check_exist = DB::table('lecture_notes')
                                ->select('lecture_notes.*')
                                ->where('lecture_notes.course_id', '=', $course_id)
                                ->where('lecture_notes.used_by', '=', $used_by)
                                ->orderByDesc('lecture_notes.note_type')
                                ->get();
                if(count($check_exist)==0){
                    $lecture_note = new Lecture_Note([
                        'course_id'        =>  $course_id,
                        'note_name'        =>  $folder_ln->note_name,
                        'note_type'        =>  'folder',
                        'note_place'       =>  'Note',
                        'status'           =>  'Active',
                        'used_by'          =>  $used_by,
                    ]);
                    $lecture_note->save();
                    $note_id = $lecture_note->ln_id;
                }else{
                    $note_id = $check_exist[0]->ln_id;
                }
                array_push($array_folder,$note_id);
            }else{
                $check_exist_folder = DB::table('lecture_notes')
                                ->select('lecture_notes.*')
                                ->where('lecture_notes.course_id', '=', $course_id)
                                ->where('lecture_notes.used_by', '=', $used_by)
                                ->orderByDesc('lecture_notes.note_type')
                                ->get();
                if(count($check_exist_folder)==0){
                    $place_id = explode(',,,',$place);
                    $note_place = "Note";
                    for($m=1;$m<=(count($place_id))-1;$m++){
                        $folder_list_ln = Lecture_Note::where('ln_id', '=', $place_id[$m])->firstOrFail();
                        $list_used_by = $folder_list_ln->used_by;
                        if($list_used_by==null){
                            $list_used_by = $folder_list_ln->ln_id;
                        }
                        $check_exist = DB::table('lecture_notes')
                                    ->select('lecture_notes.*')
                                    ->where('lecture_notes.course_id', '=', $course_id)
                                    ->where('lecture_notes.used_by', '=', $list_used_by)
                                    ->get();
                        $note_place .= ",,,".$check_exist[0]->ln_id;   
                    }
                    $lecture_note = new Lecture_Note([
                        'course_id'        =>  $course_id,
                        'note_name'        =>  $folder_ln->note_name,
                        'note_type'        =>  'folder',
                        'note_place'       =>  $note_place,
                        'status'           =>  'Active',
                        'used_by'          =>  $used_by,
                    ]);
                    $lecture_note->save();
                    $note_id = $lecture_note->ln_id;
                }else{
                    $note_id = $check_exist_folder[0]->ln_id;
                }
                array_push($array_folder,$note_id);
            }            
        }

        $i=1;
        $lnID = explode('---',($checkbox_input));
        while(isset($lnID[$i])!=""){
            $check_exist = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('lecture_notes.course_id', '=', $course_id)
                    ->where('lecture_notes.used_by', '=', $lnID[$i])
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();
            if(count($check_exist)==0){
                $ln = Lecture_Note::where('ln_id', '=', $lnID[$i])->firstOrFail();
                $place = $ln->note_place;
                $name = $ln->note_name;
                $used_by = $ln->used_by;
                $note = $ln->note;
                if($used_by == null){
                    $used_by = $ln->ln_id;
                }
                if($place!="Note"){    
                    $place_id = explode(',,,',$place);
                    $note_place = "Note";
                    for($m=1;$m<=(count($place_id))-1;$m++){
                        for($n = 0; $n<(count($unique));$n++){
                            if($place_id[$m]==$unique[$n]){
                             $note_place .= ",,,".$array_folder[$n];
                            }
                        }
                    }
                    $lecture_note = new Lecture_Note([
                            'course_id'        =>  $course_id,
                            'note_name'        =>  $name,
                            'note_type'        =>  'document',
                            'note_place'       =>  $note_place,
                            'note'             =>  $note,
                            'status'           =>  'Active',
                            'used_by'          =>  $used_by,
                    ]);
                    $lecture_note->save();   
                }else{
                   $lecture_note = new Lecture_Note([
                            'course_id'        =>  $course_id,
                            'note_name'        =>  $name,
                            'note_type'        =>  'document',
                            'note_place'       =>  'Note',
                            'note'             =>  $note,
                            'status'           =>  'Active',
                            'used_by'          =>  $used_by,
                    ]); 
                   $lecture_note->save();  
                }
            }
            $i++;
        }
        return redirect()->back()->with('success','New Document Added Successfully');
    }

    public function folder_view($folder_id)
    {
    	$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $lecture_note = Lecture_Note::where('ln_id', '=', $folder_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $lecture_note->course_id)
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
        	return view('dean.LectureNote.LectureNoteFolderView', compact('course','note_place','lecture_note','lecture_note_list','all_note','data'));
        }else{
            return redirect()->back();
        }
    }

    public function searchFiles(Request $request)
    {
    	$value         = $request->get('value');
    	$place         = $request->get('place');
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
                        $result .= '<div class="col-3" id="course_action_two">';
                            if($row->used_by==null){
                                $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                            }
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;&nbsp;<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
                          $result .= '<div class="col-3" id="course_action_two">';
                            if($row->used_by==null){
                                $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                            }
                                $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }else{
                            if($row->used_by!=null){
                                foreach($all_note as $all_row){
                                    if(($row->used_by)==($all_row->ln_id)){
                                        $semester_name = " <span style='color: grey;'>( Used In : ".$all_row->semester_name." )</span>";
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
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;&nbsp;<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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

    public function LectureNoteImage($ln_id,$image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $checkCourseId = Lecture_Note::where('ln_id', '=', $ln_id)->firstOrFail();
        $course_id = $checkCourseId->course_id;

        $course = DB::table('courses')
                 ->select('courses.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
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

        $course = Course::where('course_id', '=', $course_id)->firstOrFail();
        $lecturer = $course->lecturer;

        $user_id    = auth()->user()->user_id;
        $checkid    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $staff_id   = $checkid->id;

        if($lecturer == $staff_id){
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

        $name = $subjects[0]->subject_code." ".$subjects[0]->subject_name;
        $zip = new ZipArchive;
        $fileName = storage_path('private/Lecture_Note/Zip_Files/'.$name.'.zip');
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
                    if($note->note_place=="Note"){
                        if($note->used_by!=null){
                            foreach($all_note as $all_row){
                                if($note->used_by==$all_row->ln_id){
                                    $zip->addEmptyDir($note->note_name." (".$all_row->semester_name.")");
                                }
                            }
                        }else{
                            $zip->addEmptyDir($note->note_name);
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
                            $zip->addEmptyDir($data.'/'.$note->note_name." (".$semester.")");
                        }else{
                            $zip->addEmptyDir($data.'/'.$note->note_name);
                        }
                    }
                    $check = $note->note_place.",,,".$note->ln_id;
                    $next_check = $note->note_place.",,,".$note->ln_id.",,,";
                    $lecture_note = DB::table('lecture_notes')
                                    ->select('lecture_notes.*')
                                    ->Where(function($query) use ($check,$next_check) {
                                      $query->orWhere('note_place','LIKE','%'.$check)
                                            ->orWhere('note_place','LIKE','%'.$next_check.'%');
                                    })
                                    ->where('lecture_notes.status', '=', 'Active')
                                    ->where('lecture_notes.course_id','=',$f_course_id)
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
        return response()->download($fileName);
    }
}
