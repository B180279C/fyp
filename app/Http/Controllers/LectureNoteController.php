<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\lecture_Note;
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
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();
        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('note_place', '=', 'Note')
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();
        if(count($course)>0){
            return view('dean.viewLectureNote',compact('course','lecture_note'));
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

    public function updateFolderName(Request $request){
        $ln_id   = $request->get('ln_id');
        $lecture_note = lecture_Note::where('ln_id', '=', $ln_id)->firstOrFail();
        $lecture_note->note_name  = $request->get('folder_name');
        $lecture_note->save();
        return redirect()->back()->with('success','Edit Folder Name Successfully');
    }

    public function removeActive($id){
        $lecture_note = lecture_Note::where('ln_id', '=', $id)->firstOrFail();
        $lecture_note->status  = "Remove";
        $lecture_note->save();
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('/fake/lecture_note/'),$imageName);
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        $path = public_path().'/fake/lecture_note/'.$filename;
        if (file_exists($path)) {
            unlink($path);
        }
        return $filename;  
    }

    public function storeFiles(Request $request){

        $count = $request->get('count');
        $place = $request->get('file_place');
        for($i=1;$i<=$count;$i++){

            $name = $request->get('form'.$i);
            $ext = $request->get('ext'.$i);
            $fake = $request->get('fake'.$i);

            $lecture_note = new lecture_Note([
                'course_id'              =>  $request->get('course_id'),
                'note_name'              =>  $name,
                'note_type'              =>  'document',
                'note_place'             =>  $place,
                'note'                   =>  $fake,
                'status'                 =>  'Active',
            ]);
            $lecture_note->save();
            $fake_place = "fake/lecture_note/".$fake;
            rename($fake_place, 'Lecture_Note/'.$fake);
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
                 ->select('courses.*','subjects.*')
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
                    ->get();
        if(count($course)>0){
        	return view('dean.LectureNoteFolderView', compact('course','note_place','lecture_note','lecture_note_list','data'));
        }else{
            return redirect()->back();
        }
    }

    public function searchFiles(Request $request)
    {
    	$value         = $request->get('value');
    	$place         = $request->get('place');
        $course_id     = $request->get('course_id');
        
	    $result = "";
	    if($value!=""){
	       	$lecture_note = DB::table('lecture_notes')
	                    ->select('lecture_notes.*')
                        ->Where(function($query) use ($value) {
                          $query->orWhere('note_name','LIKE','%'.$value.'%')
                            ->orWhere('note','LIKE','%'.$value.'%');
                        })
	                    ->where('course_id', '=', $course_id)
	                    ->where('status', '=', 'Active')
	                    ->orderByDesc('lecture_notes.note_type')
	                    ->get();
	        if(count($lecture_note)>0) {
	        	foreach($lecture_note as $row){
		        	if($row->note_type=="folder"){
		            	$result .= '<a href="/lectureNote/folder/'.$row->ln_id.'" class="col-md-12 align-self-center" id="course_list">';
	                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
	                    $result .= '<div class="col-1" style="padding-top: 3px;">';
	                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
	                    $result .= '</div>';
	                    $result .= '<div class="col" id="course_name">';
		                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->note_name.'</b></p>';
		                $result .= '</div>';
		                $result .= '<div class="col-3" id="course_action_two">';
	                    $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
	                    $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
	                            </div>';
	                    $result .= '</div></a>';
	                }else{
	            		$ext = "";
                        if($row->note){
                            $ext = explode(".", $row->note);
                        }
	            		$result .= '<a download="'.$row->note_name.$ext[1].'" href="'.asset('Lecture_Note/'.$row->note).'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';
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
                        $result .= '<div class="col" id="course_name">';
	                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->note_name.'</b></p>';
	                    $result .= '</div>';
	                    $result .= '<div class="col-1" id="course_action">';
                        $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>';
                        $result .= '</div></a>';
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
	                    ->get();
	        if(count($lecture_note)>0) {
	        	foreach($lecture_note as $row){
	        		if($row->note_type=="folder"){
	            		$result .= '<a href="/lectureNote/folder/'.$row->ln_id.'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';
                        $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col" id="course_name">';
	                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->note_name.'</b></p>';
	                    $result .= '</div>';
	                    $result .= '<div class="col-3" id="course_action_two">';
                        $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                        $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>';
                        $result .= '</div></a>';
	            	}else{
	            		$ext = "";
                        if($row->note){
                            $ext = explode(".", $row->note);
                        }
	            		$result .= '<a download="'.$row->note_name.$ext[1].'" href="'.asset('Lecture_Note/'.$row->note).'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';
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
                        $result .= '<div class="col" id="course_name">';
	                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->note_name.'</b></p>';
	                    $result .= '</div>';
	                    $result .= '<div class="col-1" id="course_action">';
                        $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ln_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>';
                        $result .= '</div></a>';
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

     public function zipFileDownload($id){

        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('lecture_notes.note_type')
                    ->get();
                    
        $zip = new ZipArchive;
        $fileName = 'Lecture_Note/Zip_Files/Lecture_Note.zip';
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(public_path('/Lecture_Note/'));
        foreach($lecture_note as $row){
            if($row->note_type == "document"){
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($row->note==$relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($row->note_place=="Note"){
                            $zip->addFile($value,$row->note_name.'.'.$ext[1]);
                        }else{
                            $i=1;
                            $place = explode(',,,',$row->note_place);
                            $data = "";
                            while(isset($place[$i])!=""){
                                $name = Lecture_Note::where('ln_id', '=', $place[$i])->firstOrFail();
                                if($data==""){
                                    $data .= $name->note_name;
                                }else{
                                    $data .= "/".$name->note_name;
                                }
                                $i++;
                            }
                            $zip->addFile($value,$data.'/'.$row->note_name.'.'.$ext[1]);
                        }
                    }
                }
            }else{
                if($row->note_place=="Note"){
                    $zip->addEmptyDir($row->note_name);
                }else{
                    $i=1;
                    $place = explode(',,,',$row->note_place);
                    $data = "";
                    while(isset($place[$i])!=""){
                        $name = Lecture_Note::where('ln_id', '=', $place[$i])->firstOrFail();
                        if($data==""){
                            $data .= $name->note_name;
                        }else{
                            $data .= "/".$name->note_name;
                        }
                        $i++;
                    }
                    $zip->addEmptyDir($data.'/'.$row->note_name);
                }
            }
        }
        $zip->close();
        return response()->download(public_path($fileName));
    }
}
