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
use App\AssFinal;
use App\AssessmentFinal;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class FinalExaminationController extends Controller
{
    public function viewFinalExamination($id)
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
                    ->groupBy('courses.course_id')
                    ->get();

        if(count($course)>0){
            return view('dean.FinalExam.viewFinalExam',compact('course','previous_semester'));
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

    public function create_question($id)
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

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();
         	        
        if(count($course)>0){
            return view('dean.FinalExam.createFinalQuestion',compact('course','ass_final'));
        }else{
            return redirect()->back();
        }
    }

    public function final_assessment_list_view($fx_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $final->course_id)
                 ->get();

        $assessment_final = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('fx_id', '=', $fx_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('fx_id', '=', $fx_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('assessment_final.ass_fx_type')
                    ->get();

        if(count($course)>0){
            return view('dean.FinalExam.FinalAssessmentListView', compact('course','final','group_list','assessment_final'));
        }else{
            return redirect()->back();
        }
    }

    public function openNewAssessment(Request $request){
        $course_id    = $request->get('course_id');
        $ass_name     = $request->get('assessment_name');
        $coursework   = $request->get('coursework');

        $final = new AssFinal([
            'course_id'         =>  $course_id,
            'coursework'        =>  $coursework,
            'assessment_name'   =>  $ass_name,
            'status'            =>  'Active',
        ]);
        $final->save();

        return redirect()->back()->with('success','New Assessment Added Successfully');
    }

    public function AssessmentNameEdit(Request $request){
        $fx_id = $request->get('value');
        $folder = AssFinal::find($fx_id);
        return $folder;
    }

    public function updateAssessmentName(Request $request){
        $fx_id   = $request->get('fx_id');
        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        $final->assessment_name  = $request->get('assessment_name');
        $final->coursework       = $request->get('coursework');
        $final->save();
        return redirect()->back()->with('success','Edit Folder Name Successfully');
    }

    public function FinalAssessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $checkImageFXID = AssessmentFinal::where('ass_fx_document', '=', $image_name)->firstOrFail();
        $fx_id = $checkImageFXID->fx_id;

        $checkCourseId = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        $course_id = $checkCourseId->course_id;

        $course = DB::table('courses')
                 ->select('courses.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment_Final/' . $image_name);
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

        $assessment_final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessment_final->course_id)
                 ->get();

        $assessment_list = DB::table('assessment_final')
                    ->join('ass_final','assessment_final.fx_id','=','ass_final.fx_id')
                    ->join('courses', 'courses.course_id', '=', 'ass_final.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessment_final.*','courses.*','semesters.*','ass_final.*')
                    ->where('ass_final.course_id', '=', $assessment_final->course_id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_id')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        if(count($course)>0){
            return view('dean.FinalExam.viewWholePaper', compact('assessment_list','assessment_final'));
        }else{
            return redirect()->back();
        }
    }

    public function removeActive($id){
        $final = AssFinal::where('fx_id', '=', $id)->firstOrFail();
        $final_list = AssessmentFinal::where('fx_id','=',$id)->update(['status' => 'Remove']);
        $final->status  = "Remove";
        $final->save();
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function removeActiveList($id){
        $final_list = AssessmentFinal::where('ass_fx_id','=',$id)->update(['status' => 'Remove']);
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/assessment_final/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('fake/assessment_final/'.$filename);
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
                        $assessments = new AssessmentFinal([
                            'fx_id'                     =>  $request->get('fx_id'),
                            'ass_fx_name'               =>  $name,
                            'ass_fx_type'               =>  $request->get('ass_fx_type'),
                            'ass_fx_document'           =>  $fake,
                            'ass_fx_word'               =>  $text,   
                            'status'                    =>  'Active',
                        ]);
                        $assessments->save();
                        $fake_place = Storage::disk('private')->get("fake/assessment_final/".$fake);
                        Storage::disk('private')->put('Assessment_Final/'.$fake, $fake_place); 
                        Storage::disk('private')->delete("fake/assessment_final/".$fake);
                    }
                }
            }
        }
        return redirect()->back()->with('success','New Document Added Successfully');
    }

    public function downloadFiles($ass_fx_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment_final = AssessmentFinal::where('ass_fx_id', '=', $ass_fx_id)->firstOrFail();
        $fx_id = $assessment_final->fx_id;

        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        $course_id = $final->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

        if(count($course)>0){
            $ext = "";
            if($assessment_final->ass_fx_document!=""){
                $ext = explode(".", $assessment_final->ass_fx_document);
            }

            return Storage::disk('private')->download('Assessment_Final/'.$assessment_final->ass_fx_document,$final->assessment_name."_".$assessment_final->ass_fx_type."_".$assessment_final->ass_fx_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function searchAssessmentList(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');

        $result = "";
        if($value!=""){
            $final = DB::table('ass_final')
                        ->select('ass_final.*')
                        ->where('ass_final.assessment_name','LIKE','%'.$value.'%')
                        ->where('ass_final.status', '=', 'Active')
                        ->where('ass_final.course_id','=',$course_id)
                        ->orderBy('ass_final.assessment_name')
                        ->orderBy('ass_final.fx_id')
                        ->get();
            if(count($final)>0) {
                foreach ($final as $row) {
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-8 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->fx_id.'" value="'.$row->fx_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/FinalExamination/view_list/'.$row->fx_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.' ( '.$row->coursework.'% )</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-4" id="course_action_two">';
                    $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                        <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                    $result .= '</div>';
                    $result .= '</div> ';
                }
            }else{
                $result .= '<div class="col-md-12" style="position:relative;top:10px;">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

            if(count($ass_final)>0) {
                foreach ($ass_final as $row) {
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-8 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->fx_id.'" value="'.$row->fx_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/FinalExamination/view_list/'.$row->fx_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.' ( '.$row->coursework.'% )</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-4" id="course_action_two">';
                    $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                        <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                    $result .= '</div>';
                    $result .= '</div> ';
                }
            }else{
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">';
                $result .= '<center>Empty</center>';
                $result .= '</div>';
            }
        }
        return $result;
    }

    public function searchKey(Request $request)
    {
        $value         = $request->get('value');
        $fx_id         = $request->get('fx_id');

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        $course_id = $final->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

        $result = "";
        if($value!=""){
            $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('assessment_final.fx_id', '=', $fx_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_final.ass_fx_name','LIKE','%'.$value.'%')
                            ->orWhere('assessment_final.ass_fx_word','LIKE','%'.$value.'%');
                    })
                    ->where('assessment_final.status', '=', 'Active')
                    ->groupBy('assessment_final.ass_fx_type')
                    ->get();

            $assessment_list = DB::table('assessment_final')
                        ->select('assessment_final.*')
                        ->where('assessment_final.fx_id', '=', $fx_id)
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
                    foreach($assessment_list as $row){
                        if($row_group->ass_fx_type == $row->ass_fx_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="/images/final_assessment/'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$final->assessment_name.' / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='."/final_assessment/view/whole_paper/".$row->ass_fx_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_fx_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                          <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
            
            $assessment_final = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('fx_id', '=', $fx_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

            $group_list = DB::table('assessment_final')
                        ->select('assessment_final.*')
                        ->where('fx_id', '=', $fx_id)
                        ->where('status', '=', 'Active')
                        ->groupBy('assessment_final.ass_fx_type')
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
                            $result .= '<a href="/images/final_assessment/'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$final->assessment_name.' / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='."/final_assessment/view/whole_paper/".$row->ass_fx_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_fx_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                          <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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

    public function AllZipFileDownload($id,$download)
    {
        if($download == "checked"){
            $string = explode('_',$id);
            $course_id = $string[0];
        }else{
            $string = explode('_',$id);
            $course_id = $string[0];
        }
        
        $subjects = DB::table('subjects')
                    ->join('courses','courses.subject_id','=','subjects.subject_id')
                    ->select('courses.*','subjects.*')
                    ->where('courses.course_id', '=', $course_id)
                    ->get();

        $name = "Assessment ( ".$subjects[0]->subject_code." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment_Final/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment_Final/'));

        if($download == "checked"){
            for($i=1;$i<(count($string)-1);$i++){
                $checkASSFID = AssFinal::where('fx_id', '=', $string[$i])->firstOrFail();
                $zip->addEmptyDir($checkASSFID->assessment_name);

                $result_list = DB::table('assessment_final')
                         ->select('assessment_final.*')
                         ->where('assessment_final.fx_id', '=', $checkASSFID->fx_id)
                         ->where('assessment_final.status','=','Active')
                         ->get();

                foreach($result_list as $rl_row){
                    if($rl_row->ass_fx_type=="Question"){
                        $zip->addEmptyDir($checkASSFID->assessment_name."/Question");
                    }else{
                        $zip->addEmptyDir($checkASSFID->assessment_name."/Solution"); 
                    }
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->ass_fx_document == $relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->ass_fx_type=="Question"){
                                $zip->addFile($value,$checkASSFID->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$checkASSFID->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
                            } 
                        }
                    }
                }
            }
        }else{
            $ass_final = DB::table('ass_final')
                     ->select('ass_final.*')
                     ->where('course_id', '=', $course_id)
                     ->where('status','=','Active')
                     ->get();

            foreach($ass_final as $ass_row){
                $zip->addEmptyDir($ass_row->assessment_name);

                $result_list = DB::table('assessment_final')
                         ->select('assessment_final.*')
                         ->where('assessment_final.fx_id', '=', $ass_row->fx_id)
                         ->where('assessment_final.status','=','Active')
                         ->get();
                foreach($result_list as $rl_row){
                    if($rl_row->ass_fx_type=="Question"){
                        $zip->addEmptyDir($ass_row->assessment_name."/Question");
                    }else{
                        $zip->addEmptyDir($ass_row->assessment_name."/Solution"); 
                    }
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->ass_fx_document == $relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->ass_fx_type=="Question"){
                                $zip->addFile($value,$ass_row->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$ass_row->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
                            } 
                        }
                    }
                }
            }
        }
        $zip->close();
        return response()->download($fileName);  
    }


    public function zipFileDownload($fx_id,$download)
    {
        if($download == "checked"){
            $string = explode('---',$fx_id);
            $f_fx_id = $string[0];
        }else{
            $f_fx_id = $fx_id;
        }

        $final = AssFinal::where('fx_id', '=', $f_fx_id)->firstOrFail();

        $subjects = DB::table('subjects')
                        ->join('courses','courses.subject_id','=','subjects.subject_id')
                        ->select('courses.*','subjects.*')
                        ->where('courses.course_id', '=', $final->course_id)
                        ->get();

        $name = $subjects[0]->subject_code." ( Final ) ";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment_Final/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment_Final/'));
        
        if($download == "checked"){
            for($i=1;$i<(count($string)-1);$i++){
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
                         ->where('assessment_final.fx_id', '=', $f_fx_id)
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
        return response()->download($fileName);
    }
}
