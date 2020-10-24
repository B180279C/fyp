<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Assessment;
use App\Imports\syllabusRead;

class AssessmentController extends Controller
{
    public function viewAssessment($id)
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
                    ->select('subjects.*','courses.*','semesters.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('courses.semester')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.viewAssessment',compact('course','previous_semester'));
        }else{
            return redirect()->back();
        }
    }

    public function getSyllabusData(Request $request)
    {
        $id = $request->get('course_id');
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

        if(count($course)>0){
            $path = storage_path('private/syllabus/'.$course[0]->syllabus);
            $array = (new syllabusRead)->toArray($path);
            // return response()->json([$array[0],$course]);
            return response()->json($array[0]);
        }else{
            return redirect()->back();
        }      
    }

    public function create_question($id,$question)
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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('ass_place', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.ass_name')
                    ->get();

        $previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('subjects.*','courses.*','semesters.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('courses.semester')
                    ->get();

        $group_assessments = DB::table('assessments')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->select('assessments.*','courses.*')
                    ->where('courses.subject_id','=',$course[0]->subject_id)
                    ->where('assessments.assessment', '=', $question)
                    ->where('assessments.status', '=', 'Active')
                    ->groupBy('assessments.course_id')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.createQuestion',compact('course','question','assessments','previous_semester','group_assessments'));
        }else{
            return redirect()->back();
        }
    }

    public function assessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $checkImageCID = Assessment::where('ass_document', '=', $image_name)->firstOrFail();
        $course_id = $checkImageCID->course_id;

        $course = DB::table('courses')
                 ->select('courses.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();
        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function openNewFolder(Request $request){
        $course_id    = $request->get('course_id');
        $ass_name     = $request->get('folder_name');
        $assessment   = $request->get('assessment');
        $type         = "folder";
        $place        = $request->get('folder_place');

        $assessment = new Assessment([
            'course_id'         =>  $course_id,
            'assessment'        =>  $assessment,
            'ass_name'          =>  $ass_name,
            'ass_type'          =>  $type,
            'ass_place'         =>  $place,
            'status'            =>  'Active',
        ]);
        $assessment->save();

        return redirect()->back()->with('success','New Folder Added Successfully');
    }

    public function folderNameEdit(Request $request){
        $folder_id = $request->get('value');
        $folder = Assessment::find($folder_id);
        return $folder;
    }

    public function updateFolderName(Request $request){
        $ass_id   = $request->get('ass_id');
        $assessment = Assessment::where('ass_id', '=', $ass_id)->firstOrFail();
        $assessment->ass_name  = $request->get('folder_name');
        $assessment->save();
        return redirect()->back()->with('success','Edit Folder Name Successfully');
    }

    public function removeActive($id){
        $assessment = Assessment::where('ass_id', '=', $id)->firstOrFail();
        $assessment->status  = "Remove";
        $assessment->save();
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/assessment/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('fake/assessment/'.$filename);
        return $filename;  
    }

    public function storeFiles(Request $request){

        $count = $request->get('count');
        $place = $request->get('file_place');
        $array[] = "";
        for($i=1;$i<=$count;$i++){
            $name = $request->get('form'.$i);
            $ext  = $request->get('ext'.$i);
            $fake = $request->get('fake'.$i);
            $text = $request->get('text'.$i);

            array_push($array, $name);
            sort($array);
        }
        for ($m=0; $m < count($array); $m++) {    
            $value = $array[$m];
            if($value!=""){
                for($i=1;$i<=$count;$i++){
                    $name = $request->get('form'.$i);
                    $ext  = $request->get('ext'.$i);
                    $fake = $request->get('fake'.$i);
                    $text = $request->get('text'.$i);
                    if($value == $name){
                        $assessments = new Assessment([
                            'course_id'              =>  $request->get('course_id'),
                            'assessment'             =>  $request->get('assessment'),
                            'ass_name'               =>  $name,
                            'ass_type'               =>  'document',
                            'ass_place'              =>  $place,
                            'ass_document'           =>  $fake,
                            'ass_word'               =>  $text,   
                            'status'                 =>  'Active',
                        ]);
                        $assessments->save();
                        $fake_place = Storage::disk('private')->get("fake/assessment/".$fake);
                        Storage::disk('private')->put('Assessment/'.$fake, $fake_place); 
                        Storage::disk('private')->delete("fake/assessment/".$fake);
                    }
                }
            }
        }
        return redirect()->back()->with('success','New Document Added Successfully');
    }

    public function folder_view($folder_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment = Assessment::where('ass_id', '=', $folder_id)->firstOrFail();

        $question = $assessment->assessment;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessment->course_id)
                 ->get();
        
        $place_name = explode(',,,',($assessment->ass_place));
        $i=1;
        $data = $question;
        while(isset($place_name[$i])!=""){
            $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
            $data .= ",,,".$name->ass_name;
            $i++;
        }

        $ass_place = $assessment->ass_place.",,,".$assessment->ass_id;

        $assessment_list = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $assessment->course_id)
                    ->where('ass_place', '=', $ass_place)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.ass_id')
                    ->orderBy('assessments.ass_name')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.AssessmentFolderView', compact('course','ass_place','assessment','assessment_list','data','question'));
        }else{
            return redirect()->back();
        }
    }

    public function view_wholePaper($ass_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment = Assessment::where('ass_id', '=', $ass_id)->firstOrFail();

        $question = $assessment->assessment;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessment->course_id)
                 ->get();

        $place_name = explode(',,,',($assessment->ass_place));
        $i=1;
        $data = $question;
        while(isset($place_name[$i])!=""){
            $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
            $data .= " / ".$name->ass_name;
            $i++;
        }

        $assessment_list = DB::table('assessments')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessments.*','courses.*','semesters.*')
                    ->where('assessments.course_id', '=', $assessment->course_id)
                    ->where('assessments.ass_place', '=', $assessment->ass_place)
                    ->where('assessments.assessment','=',$assessment->assessment)
                    ->where('assessments.ass_type','=','document')
                    ->where('assessments.status', '=', 'Active')
                    ->orderBy('assessments.ass_id')
                    ->orderBy('assessments.ass_name')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.viewWholePaper', compact('assessment_list','data'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadFiles($ass_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment = Assessment::where('ass_id', '=', $ass_id)->firstOrFail();

        $question = $assessment->assessment;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessment->course_id)
                 ->get();

        if(count($course)>0){
            $ext = "";
            if($assessment->ass_document!=""){
                $ext = explode(".", $assessment->ass_document);
            }

            $place_name = explode(',,,',($assessment->ass_place));
            $i=1;
            $data = $question;
            while(isset($place_name[$i])!=""){
                $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
                $data .= "_".$name->ass_name;
                $i++;
            }

            return Storage::disk('private')->download('Assessment/'.$assessment->ass_document,$data."_".$assessment->ass_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function viewPreviousQuestion($id,$course_id,$question,$list){
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

        $assessments = DB::table('assessments')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('assessments.*','courses.*','semesters.*')
                    ->where('assessments.course_id', '=', $course_id)
                    ->where('assessments.ass_place', '=', $question)
                    ->where('assessments.status', '=', 'Active')
                    ->orderBy('assessments.ass_id')
                    ->orderBy('assessments.ass_name')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.viewPreviousQuestion',compact('course','question','assessments','list'));
        }else{
            return redirect()->back();
        }
    }

    public function previous_folder_view($id,$folder_id,$list)
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

        $assessments = DB::table('assessments')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('assessments.*','courses.*','semesters.*')
                    ->where('assessments.ass_id', '=', $folder_id)
                    ->get();

        $question = $assessments[0]->assessment;
        
        $place_name = explode(',,,',($assessments[0]->ass_place));
        $i=1;
        $data = $question;
        while(isset($place_name[$i])!=""){
            $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
            $data .= ",,,".$name->ass_name;
            $i++;
        }

        $ass_place = $assessments[0]->ass_place.",,,".$assessments[0]->ass_id;

        $assessment_list = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $assessments[0]->course_id)
                    ->where('ass_place', '=', $ass_place)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.ass_name')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.AssessmentPreviousFolderView', compact('course','ass_place','assessments','assessment_list','data','question','list'));
        }else{
            return redirect()->back();
        }
    }

    public function searchKey(Request $request)
    {
        $value         = $request->get('value');
        $place         = $request->get('place');
        $course_id     = $request->get('course_id');
        $question      = $request->get('question');

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $result = "";
        if($value!=""){
            $current_assessments = DB::table('assessments')
                        ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                        ->select('assessments.*','courses.*','semesters.*')
                        ->where('courses.subject_id','=',$course[0]->subject_id)
                        ->Where(function($query) use ($value) {
                          $query->orWhere('assessments.ass_name','LIKE','%'.$value.'%')
                            ->orWhere('assessments.ass_word','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
                        })
                        ->where('assessments.assessment', '=', $question)
                        ->where('assessments.status', '=', 'Active')
                        ->orderBy('assessments.ass_type')
                        ->orderByDesc('semesters.semester_name')
                        ->orderBy('assessments.ass_name')
                        ->orderBy('assessments.ass_id')
                        ->get();
            if($place == $question){
                $result .= '<h5 style="margin-top: -15px;padding-left: 15px;">Searched Result</h5>';
            }else{
                $result .= '<h5 style="margin-top: 5px;padding-left: 15px;">Searched Result</h5>';
            }
            if(count($current_assessments)>0) {
                foreach($current_assessments as $row){
                    if($row->ass_place==$row->assessment){
                        $original_place = $row->semester_name." : ";
                        $title = $row->semester_name." : ".$row->assessment.' / '.$row->ass_name;
                    }else{
                        $original_place = $row->semester_name." : ";
                        $place_name = explode(',,,',($row->ass_place));
                        $title = $row->semester_name." : ";
                        $i=1;
                        while(isset($place_name[$i])!=""){
                            $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
                            $original_place .= $name->ass_name." / ";
                            $title .= $name->ass_name." / ";
                            $i++;
                        }
                        $title .= $row->ass_name;
                    }
                    if($row->ass_type=="folder"){
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        if($course[0]->semester_name==$row->semester_name){
                            $result .= '<a href="/assessment/folder/'.$row->ass_id.'" id="show_image_link" class="col-9 row align-self-center">';
                        }else{
                            $result .= '<a href="/assessment/folder/'.$course[0]->course_id.'/previous/'.$row->ass_id.'/once" id="show_image_link" class="col-9 row align-self-center">';
                        }
                        $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->semester_name." : ".$row->ass_name.'</b></p>';
                        $result .= '</div></div></a>';
                        if($course[0]->semester_name==$row->semester_name){
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                        <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div></div>';
                        }else{
                            $result .= '</div>';
                        }
                    }else{
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-9 row align-self-center" id="show_image_link" data-title="'.$title.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                        $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="course_name">';

                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$original_place. '<span style="color:black;">' .$row->ass_name. '</span></b></p>';
                        $result .= '</div></div></a>';
                        if($course[0]->semester_name==$row->semester_name){
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                        <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div></div>';
                        }else{
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                            $result .= '</div></div>';
                        }
                    }
                }
            }else{
                $result .= '<div class="col-md-12">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $current_assessments = DB::table('assessments')
                        ->select('assessments.*')
                        ->where('course_id', '=', $course_id)
                        ->where('ass_place', '=', $place)
                        ->where('status', '=', 'Active')
                        ->get();
            if(count($current_assessments)>0) {
                if($place == $question){
                    $result .= '<h5 style="margin-top: -15px;padding-left: 15px;">Current Semester</h5>';
                }
                foreach($current_assessments as $row){
                    if($row->ass_place==$row->assessment){
                        $title = $row->assessment.' / '.$row->ass_name;
                    }else{
                        $place_name = explode(',,,',($row->ass_place));
                        $title = $row->assessment.' / ';
                        $i=1;
                        while(isset($place_name[$i])!=""){
                            $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
                            $title .= $name->ass_name." / ";
                            $i++;
                        }
                    }
                    if($row->ass_type=="folder"){
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<a href="/assessment/folder/'.$row->ass_id.'" id="show_image_link" class="col-9 row align-self-center">';
                        $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->ass_name.'</b></p>';
                        $result .= '</div></div></a>';
                        $result .= '<div class="col-3" id="course_action_two">';
                        $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                    <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                        $result .= '</div></div>';
                    }else{
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-9 row align-self-center" id="show_image_link" data-title="'.$title.$row->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                        $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="course_name">';

                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;color:black;" id="file_name"><b>'.$row->ass_name.'</b></p>';
                        $result .= '</div></div></a>';
                        $result .= '<div class="col-3" id="course_action_two">';
                        $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                    <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                        $result .= '</div></div>';
                    }
                }
            }
        }
        return $result;
    }


    public function viewPreviousAssessment($id,$course_id)
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

        $assessments = DB::table('assessments')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->select('assessments.*','courses.*','semesters.*')
                    ->where('assessments.course_id', '=', $course_id)
                    ->where('assessments.status', '=', 'Active')
                    ->groupBy('assessments.assessment')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.viewPreviousAssessment',compact('course','assessments'));
        }else{
            return redirect()->back();
        }
    }

    public function searchListKey(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $course_id)
                 ->get();

        $result = "";
        if($value!=""){
            $grouping = DB::table('assessments')
                        ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                        ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                        ->select('assessments.*','courses.*','semesters.*')
                        ->where('courses.subject_id','=',$course[0]->subject_id)
                        ->Where(function($query) use ($value) {
                          $query->orWhere('assessments.ass_name','LIKE','%'.$value.'%')
                            ->orWhere('assessments.ass_word','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
                        })
                        ->where('assessments.course_id','!=',$course_id)
                        ->where('assessments.status', '=', 'Active')
                        ->groupBy('assessments.assessment')
                        ->orderBy('assessments.assessment')
                        ->get();

            $result .= '<h5 style="margin-top: 5px;padding-left: 15px;border:0px solid black;" class="col-md-12">Searched Result</h5>';

            $result .= '<hr style="color:#d9d9d9;margin:0px 15px;position:relative;top:3px;" class="col">';
            $p = 1;
            if(count($grouping) >0 ){
                foreach($grouping as $ga){
                    $current_assessments = DB::table('assessments')
                            ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                            ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                            ->select('assessments.*','courses.*','semesters.*')
                            ->where('courses.subject_id','=',$course[0]->subject_id)
                            ->Where(function($query) use ($value) {
                              $query->orWhere('assessments.ass_name','LIKE','%'.$value.'%')
                                ->orWhere('assessments.ass_word','LIKE','%'.$value.'%')
                                ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
                            })
                            ->where('assessments.course_id','!=',$course_id)
                            ->where('assessments.assessment', '=', $ga->assessment)
                            ->where('assessments.status', '=', 'Active')
                            ->orderBy('assessments.ass_type')
                            ->orderByDesc('semesters.semester_name')
                            ->orderBy('assessments.ass_name')
                            ->orderBy('assessments.ass_id')
                            ->get();

                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<h5 style="padding:15px 0px 15px 15px;margin:0px;" class="col-md-12 plus" id="'.$p.'">'.$ga->assessment.' (<i class="fa fa-plus" aria-hidden="true" id="icon_'.$p.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '<div id="previous_'.$p.'" class="col-12 row align-self-center list" style="display:none;margin-left:0px;padding:0px;">';
                    foreach($current_assessments as $row){
                        if($row->ass_place==$row->assessment){
                            $original_place = $row->semester_name." : ";
                            $title = $row->semester_name." : ".$row->assessment.' / '.$row->ass_name;
                        }else{
                            $original_place = $row->semester_name." : ";
                            $place_name = explode(',,,',($row->ass_place));
                            $title = $row->semester_name." : ";
                            $i=1;
                            while(isset($place_name[$i])!=""){
                                $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
                                $original_place .= $name->ass_name." / ";
                                $title .= $name->ass_name." / ";
                                $i++;
                            }
                            $title .= $row->ass_name;
                        }
                        if($row->ass_type=="folder"){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<a href="/assessment/folder/'.$course[0]->course_id.'/previous/'.$row->ass_id.'/list" id="show_image_link" class="col-9 row align-self-center">';
                            $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->semester_name." : ".$row->ass_name.'</b></p>';
                            $result .= '</div></div></a>';
                            $result .= '</div>';
                        }else{
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-9 row align-self-center" id="show_image_link" data-title="'.$title.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';

                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$original_place. '<span style="color:black;">' .$row->ass_name. '</span></b></p>';
                            $result .= '</div></div></a>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                            $result .= '</div></div>';
                        }
                    }
                    $result .= '</div></div>';
                    $p++;
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
                    ->select('subjects.*','courses.*','semesters.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('courses.semester')
                    ->get();
            foreach($previous_semester as $row){
                $result .= '<div class="col-12 row align-self-center" id="course_list">';
                $result .= '<a href="/assessment/'.$course_id.'/previous/'.$row->course_id.'/list" id="show_image_link" class="col-9 row align-self-center">';
                $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col-10" id="course_name">';
                $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->semester_name.'</b></p>';
                $result .= '</div>';
                $result .= '</div>';
                $result .= '</a>';
                $result .= '</div>';
            }
        }
        return $result;
    }
}
