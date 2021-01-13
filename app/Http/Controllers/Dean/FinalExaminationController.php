<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\User;
use App\Staff;
use App\Subject;
use App\Department;
use App\Faculty;
use App\AssFinal;
use App\AssessmentFinal;
use App\Imports\syllabusRead;
use ZipArchive;
use File;
use App\ActionFA_V_A;

class FinalExaminationController extends Controller
{
    public function viewFinalExamination($id)
    {
    	$user_id     = auth()->user()->user_id;
      $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
      $faculty_id  = $staff_dean->faculty_id;
      $department_id = $staff_dean->department_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','users.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

        $action = DB::table('actionfa_v_a')
                  ->select('actionfa_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('actionFA_id')
                  ->get();

        $assessment_final = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        $moderator_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
        $moderator_person_name = User::where('user_id', '=', $moderator_by->user_id)->firstOrFail();

        $verified_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        $approved_by = Staff::where('id', '=', $course[0]->approved_by)->firstOrFail();
        $approved_person_name = User::where('user_id', '=', $approved_by->user_id)->firstOrFail();

        if(count($course)>0){
            return view('dean.FinalExam.viewFinalExam',compact('course','ass_final','action','moderator_person_name','verified_person_name','approved_person_name','assessment_final'));
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

        $lecturer_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        $assessment_final = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        if(count($course)>0){
            $path = storage_path('private/syllabus/'.$course[0]->syllabus);
            $array = (new syllabusRead)->toArray($path);
            return response()->json([$array[0],$lecturer_result,$assessment_final]);
        }else{
            return redirect()->back();
        }      
    }

    public function create_final_list($coursework,$id)
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

        $assessment_final = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->groupBy('assessment_final.ass_fx_type')
                    ->get();

        $mark = 0;
        foreach ($ass_final as $row){
            $mark = $mark+$row->coursework;
        }
                  
        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $tp = DB::table('teaching_plan')
                  ->join('plan_topics','teaching_plan.tp_id','=','plan_topics.tp_id')
                  ->select('teaching_plan.*','plan_topics.*')
                  ->where('teaching_plan.course_id', '=', $id)
                  ->groupBy('plan_topics.lecture_topic')
                  ->get();

        if(count($course)>0){
            return view('dean.FinalExam.createFinalList',compact('course','mark','coursework','ass_final','TP_Ass','tp','group_list','assessment_final'));
        }else{
            return redirect()->back();
        }
    }

    public function create_question($coursework,$id)
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

        $assessment_final = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->groupBy('assessment_final.ass_fx_type')
                    ->get();

        $mark = 0;
        foreach ($ass_final as $row){
            $mark = $mark+$row->coursework;
        }
         	        
        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $tp = DB::table('teaching_plan')
                  ->join('plan_topics','teaching_plan.tp_id','=','plan_topics.tp_id')
                  ->select('teaching_plan.*','plan_topics.*')
                  ->where('teaching_plan.course_id', '=', $id)
                  ->groupBy('plan_topics.lecture_topic')
                  ->get();

        if(count($course)>0){
            return view('dean.FinalExam.createFinalQuestion',compact('course','mark','coursework','ass_final','TP_Ass','tp','group_list','assessment_final'));
        }else{
            return redirect()->back();
        }
    }

    // public function final_assessment_list_view($fx_id)
    // {
    //     $user_id       = auth()->user()->user_id;
    //     $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    //     $faculty_id    = $staff_dean->faculty_id;

    //     $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();

    //     $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('lecturer', '=', $staff_dean->id)
    //              ->where('course_id', '=', $final->course_id)
    //              ->get();

    //     $assessment_final = DB::table('assessment_final')
    //                 ->select('assessment_final.*')
    //                 ->where('fx_id', '=', $fx_id)
    //                 ->where('status', '=', 'Active')
    //                 ->orderBy('assessment_final.ass_fx_name')
    //                 ->get();

    //     $group_list = DB::table('assessment_final')
    //                 ->select('assessment_final.*')
    //                 ->where('fx_id', '=', $fx_id)
    //                 ->where('status', '=', 'Active')
    //                 ->groupBy('assessment_final.ass_fx_type')
    //                 ->get();

    //     if(count($course)>0){
    //         return view('dean.FinalExam.FinalAssessmentListView', compact('course','final','group_list','assessment_final'));
    //     }else{
    //         return redirect()->back();
    //     }
    // }

    public function openNewAssessment(Request $request){
        $course_id    = $request->get('course_id');
        $ass_name     = $request->get('assessment_name');
        $CLO = array();
        $CLO = $request->get('CLO');
        $coursework = $request->get('coursework');
        $CLO_ALL = $request->get('CLO_ALL');
        $total = $request->get('total');
        $CLO_List = "";
        if($CLO!=null){  
          foreach($CLO as $value){
            $CLO_List .= $value.',';
          }
        }

        $topic = array();
        $topic = $request->get('topic');
        $topic_List = "";
        if($topic!=null){  
          foreach($topic as $value){
            $topic_List .= $value.',';
          }
        }

        $final = new AssFinal([
            'course_id'         =>  $course_id,
            'topic'             =>  $topic_List,
            'CLO'               =>  $CLO_List,
            'coursemark'        =>  $total,
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

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $folder->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $folder->course_id)
                  ->get();

        $tp = DB::table('teaching_plan')
                  ->join('plan_topics','teaching_plan.tp_id','=','plan_topics.tp_id')
                  ->select('teaching_plan.*','plan_topics.*')
                  ->where('teaching_plan.course_id', '=', $folder->course_id)
                  ->where('plan_topics.lecture_topic','!=',null)
                  ->groupBy('plan_topics.lecture_topic')
                  ->get();

        return [$folder,$ass_final,$TP_Ass,$tp];
    }

    public function updateAssessmentName(Request $request){
        $fx_id   = $request->get('fx_id');
        $CLO = array();
        $CLO = $request->get('CLO');
        $CLO_ALL = $request->get('CLO_ALL');
        $CLO_List = "";
        if($CLO!=null){  
          foreach($CLO as $value){
            $CLO_List .= $value.',';
          }
        }

        $topic = array();
        $topic = $request->get('topic');
        $topic_List = "";
        if($topic!=null){  
          foreach($topic as $value){
            $topic_List .= $value.',';
          }
        }

        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        $final->assessment_name  = $request->get('assessment_name');
        $final->CLO              = $CLO_List;
        $final->topic            = $topic_List;
        $final->coursework       = $request->get('coursework');
        $final->coursemark  = $request->get('total');
        $final->save();
        return redirect()->back()->with('success','Edit Assessment Detail Successfully');
    }

    public function FinalAssessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $checkImageFXID = AssessmentFinal::where('ass_fx_document', '=', $image_name)->firstOrFail();
        $course_id = $checkImageFXID->course_id;

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

    public function view_wholePaper($course_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

        $assessment_list = DB::table('assessment_final')
                    ->join('courses','assessment_final.course_id','=','courses.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessment_final.*','courses.*','semesters.*')
                    ->where('assessment_final.course_id', '=', $course_id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_id')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        if(count($course)>0){
            return view('dean.FinalExam.viewWholePaper', compact('assessment_list'));
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
                            'course_id'                 =>  $request->get('course_id'),
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
        $course_id = $assessment_final->course_id;

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

            return Storage::disk('private')->download('Assessment_Final/'.$assessment_final->ass_fx_document,"Final_".$assessment_final->ass_fx_type."_".$assessment_final->ass_fx_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    // public function searchAssessmentList(Request $request)
    // {
    //     $value         = $request->get('value');
    //     $course_id     = $request->get('course_id');

    //     if(auth()->user()->position=="Dean"){
    //         $character = '';
    //     }else if(auth()->user()->position=="HoD"){
    //         $character = '/hod';
    //     }else if(auth()->user()->position=="Lecturer"){
    //         $character = '/lecturer';
    //     }

    //     $result = "";
    //     if($value!=""){
    //         $final = DB::table('ass_final')
    //                     ->select('ass_final.*')
    //                     ->where('ass_final.assessment_name','LIKE','%'.$value.'%')
    //                     ->where('ass_final.status', '=', 'Active')
    //                     ->where('ass_final.course_id','=',$course_id)
    //                     ->orderBy('ass_final.assessment_name')
    //                     ->orderBy('ass_final.fx_id')
    //                     ->get();
    //         if(count($final)>0) {
    //             foreach ($final as $row) {
    //                 $result .= '<div class="col-12 row align-self-center" id="course_list">';
    //                 $result .= '<div class="col-8 row align-self-center" style="padding-left: 20px;">';
    //                 $result .= '<div class="checkbox_style align-self-center">';
    //                 $result .= '<input type="checkbox" name="group'.$row->fx_id.'" value="'.$row->fx_id.'" class="group_download">';
    //                 $result .= '</div>';
    //                 $result .= '<a href="'.$character.'/FinalExamination/view_list/'.$row->fx_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
    //                 $result .= '<div class="col-1" style="position: relative;top: -2px;">';
    //                 $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
    //                 $result .= '</div>';
    //                 $result .= '<div class="col-10" id="course_name">';
    //                 $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.' ( '.$row->coursework.'% )</b></p>';
    //                 $result .= '</div>';
    //                 $result .= '</a>';
    //                 $result .= '</div>';
    //                 $result .= '<div class="col-4" id="course_action_two">';
    //                 $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
    //                     <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
    //                 $result .= '</div>';
    //                 $result .= '</div> ';
    //             }
    //         }else{
    //             $result .= '<div class="col-md-12" style="position:relative;top:10px;">';
    //             $result .= '<p>Not Found</p>';
    //             $result .= '</div>';
    //         }
    //     }else{
    //         $ass_final = DB::table('ass_final')
    //                 ->select('ass_final.*')
    //                 ->where('course_id', '=', $course_id)
    //                 ->where('status', '=', 'Active')
    //                 ->orderBy('ass_final.assessment_name')
    //                 ->get();

    //         if(count($ass_final)>0) {
    //             foreach ($ass_final as $row) {
    //                 $result .= '<div class="col-12 row align-self-center" id="course_list">';
    //                 $result .= '<div class="col-8 row align-self-center" style="padding-left: 20px;">';
    //                 $result .= '<div class="checkbox_style align-self-center">';
    //                 $result .= '<input type="checkbox" name="group'.$row->fx_id.'" value="'.$row->fx_id.'" class="group_download">';
    //                 $result .= '</div>';
    //                 $result .= '<a href="'.$character.'/FinalExamination/view_list/'.$row->fx_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
    //                 $result .= '<div class="col-1" style="position: relative;top: -2px;">';
    //                 $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
    //                 $result .= '</div>';
    //                 $result .= '<div class="col-10" id="course_name">';
    //                 $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.' ( '.$row->coursework.'% )</b></p>';
    //                 $result .= '</div>';
    //                 $result .= '</a>';
    //                 $result .= '</div>';
    //                 $result .= '<div class="col-4" id="course_action_two">';
    //                 $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
    //                     <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
    //                 $result .= '</div>';
    //                 $result .= '</div> ';
    //             }
    //         }else{
    //             $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">';
    //             $result .= '<center>Empty</center>';
    //             $result .= '</div>';
    //         }
    //     }
    //     return $result;
    // }

    public function searchKey(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $course_id)
                 ->get();

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $result = "";
        if($value!=""){
            $group_list = DB::table('assessment_final')
                    ->select('assessment_final.*')
                    ->where('assessment_final.course_id', '=', $course_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_final.ass_fx_name','LIKE','%'.$value.'%')
                            ->orWhere('assessment_final.ass_fx_word','LIKE','%'.$value.'%');
                    })
                    ->where('assessment_final.status', '=', 'Active')
                    ->groupBy('assessment_final.ass_fx_type')
                    ->get();

            $assessment_list = DB::table('assessment_final')
                        ->select('assessment_final.*')
                        ->where('assessment_final.course_id', '=', $course_id)
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
                    $result .= '<h5 class="group plus" id="plus_'.$i.'">'.$row_group->ass_fx_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_list as $row){
                        if($row_group->ass_fx_type == $row->ass_fx_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/images/final_assessment/'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : Final / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='.$character."/final_assessment/view/whole_paper/".$row->course_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
                    ->where('course_id', '=', $course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

            $group_list = DB::table('assessment_final')
                        ->select('assessment_final.*')
                        ->where('course_id', '=', $course_id)
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
                    $result .= '<h5 class="group plus" id="plus_'.$i.'">'.$row_group->ass_fx_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_final as $row){
                        if($row_group->ass_fx_type == $row->ass_fx_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/images/final_assessment/'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : Final / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='.$character."/final_assessment/view/whole_paper/".$row->course_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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

    // public function AllZipFileDownload($id,$download)
    // {
    //     if($download == "checked"){
    //         $string = explode('_',$id);
    //         $course_id = $string[0];
    //     }else{
    //         $string = explode('_',$id);
    //         $course_id = $string[0];
    //     }
        
    //     $subjects = DB::table('subjects')
    //                 ->join('courses','courses.subject_id','=','subjects.subject_id')
    //                 ->select('courses.*','subjects.*')
    //                 ->where('courses.course_id', '=', $course_id)
    //                 ->get();

    //     $ZipFile_name = "Assessment ( ".$subjects[0]->subject_code." )";
    //     $zip = new ZipArchive;
    //     $fileName = storage_path('private/Assessment_Final/Zip_Files/'.$ZipFile_name.'.zip');
    //     $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    //     $files = File::files(storage_path('/private/Assessment_Final/'));

    //     if($download == "checked"){
    //         for($i=1;$i<(count($string)-1);$i++){
    //             $checkASSFID = AssFinal::where('fx_id', '=', $string[$i])->firstOrFail();
    //             $zip->addEmptyDir($checkASSFID->assessment_name);

    //             $result_list = DB::table('assessment_final')
    //                      ->select('assessment_final.*')
    //                      ->where('assessment_final.fx_id', '=', $checkASSFID->fx_id)
    //                      ->where('assessment_final.status','=','Active')
    //                      ->get();

    //             foreach($result_list as $rl_row){
    //                 if($rl_row->ass_fx_type=="Question"){
    //                     $zip->addEmptyDir($checkASSFID->assessment_name."/Question");
    //                 }else{
    //                     $zip->addEmptyDir($checkASSFID->assessment_name."/Solution"); 
    //                 }
    //                 foreach ($files as $key => $value) {
    //                     $relativeNameInZipFile = basename($value);
    //                     if($rl_row->ass_fx_document == $relativeNameInZipFile){
    //                         $ext = explode('.',$relativeNameInZipFile);
    //                         if($rl_row->ass_fx_type=="Question"){
    //                             $zip->addFile($value,$checkASSFID->assessment_name."/Question/".$rl_row->ass_fx_name.'.'.$ext[1]);
    //                         }else{
    //                             $zip->addFile($value,$checkASSFID->assessment_name."/Solution/".$rl_row->ass_fx_name.'.'.$ext[1]);
    //                         } 
    //                     }
    //                 }
    //             }
    //         }
    //     }else{
    //         $ass_final = DB::table('ass_final')
    //                  ->select('ass_final.*')
    //                  ->where('course_id', '=', $course_id)
    //                  ->where('status','=','Active')
    //                  ->get();

    //         foreach($ass_final as $ass_row){
    //             $zip->addEmptyDir($ass_row->assessment_name);

    //             $result_list = DB::table('assessment_final')
    //                      ->select('assessment_final.*')
    //                      ->where('assessment_final.fx_id', '=', $ass_row->fx_id)
    //                      ->where('assessment_final.status','=','Active')
    //                      ->get();
    //             foreach($result_list as $rl_row){
    //                 if($rl_row->ass_fx_type=="Question"){
    //                     $zip->addEmptyDir($ass_row->assessment_name."/Question");
    //                 }else{
    //                     $zip->addEmptyDir($ass_row->assessment_name."/Solution"); 
    //                 }
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
    //     if($this->checkCoursePerson($course_id)==true){
    //         return response()->download($fileName)->deleteFileAfterSend(true);
    //     }else{
    //         Storage::disk('private')->delete('/Assessment_Final/Zip_Files/'.$ZipFile_name.'.zip');
    //         return redirect()->back();
    //     } 
    // }


    public function zipFileDownload($fx_id,$download)
    {
        if($download == "checked"){
            $string = explode('---',$fx_id);
            $f_course_id = $string[0];
        }else{
            $f_course_id = $fx_id;
        }

        $subjects = DB::table('subjects')
                        ->join('courses','courses.subject_id','=','subjects.subject_id')
                        ->select('courses.*','subjects.*')
                        ->where('courses.course_id', '=', $f_course_id)
                        ->get();

        $ZipFile_name = $subjects[0]->subject_code." ( Final ) ";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment_Final/Zip_Files/'.$ZipFile_name.'.zip');
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
                         ->where('assessment_final.course_id', '=', $f_course_id)
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
            Storage::disk('private')->delete('/Assessment_Final/Zip_Files/'.$ZipFile_name.'.zip');
            return redirect()->back();
        }
    }

    public function FASubmitAction($id)
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
        if(count($course)>0){
          $action = new ActionFA_V_A([
            'course_id'   => $id,
            'status'      => "Waiting For Moderation",
            'for_who'     => "Moderator",
          ]);
          $action->save();
          return redirect()->back()->with('success','Final Assessment Submitted to Moderator Successfully');
        }else{
          return redirect()->back();
        }
    }

    public function SubmitSelf_D_Form(Request $request)
    {
        $actionFA_id = $request->get('actionFA_id');
        $status = $request->get('status');
        $course_id = $request->get('course_id');
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                  ->select('courses.*','subjects.*','semesters.*')
                  ->where('lecturer', '=', $staff_dean->id)
                  ->where('course_id', '=', $course_id)
                  ->get();
        if(count($course)>0){
            $action = ActionFA_V_A::where('actionFA_id', '=', $actionFA_id)->firstOrFail();
            $action->status  = 'Waiting For Verified';
            $action->for_who = 'HOD';
            $action->self_declaration = $status;
            $action->self_date = date("Y-j-n");
            $action->save();
            return redirect()->back()->with('success','Continuous Assessment Submitted to HOD Successfully');
        }else{
            return redirect()->back();
        }
    }

    public function ModerationFormReport($actionFA_id)
  {
    $user_id       = auth()->user()->user_id;
    $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    $faculty_id    = $staff_dean->faculty_id;
    $department_id = $staff_dean->department_id;

    $action = ActionFA_V_A::where('actionFA_id', '=', $actionFA_id)->firstOrFail();

    $course = DB::table('courses')
                   ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                   ->join('programmes','subjects.programme_id','=','programmes.programme_id')
                   ->join('departments','programmes.department_id','=','departments.department_id')
                   ->join('faculty','departments.faculty_id','=','faculty.faculty_id')
                   ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                   ->join('staffs','staffs.id','=','courses.lecturer')
                   ->join('users','staffs.user_id','=','users.user_id')
                   ->select('courses.*','subjects.*','semesters.*','programmes.*','staffs.*','users.*','faculty.*')
                   ->where('courses.lecturer', '=', $staff_dean->id)
                   ->where('course_id', '=', $action->course_id)
                   ->get();

    $Moderator = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id', '=', $course[0]->moderator)
                 ->get();

    $verified_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id', '=', $course[0]->verified_by)
                 ->get();

    $approved_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id', '=', $course[0]->approved_by)
                 ->get();

    // $verified_by = DB::table('staffs')
    //              ->join('users','staffs.user_id','=','users.user_id')
    //              ->select('staffs.*','users.*')
    //              ->where('users.position', '=', 'HoD')
    //              ->where('staffs.department_id','=',$department_id)
    //              ->get();

    // $approved_by = DB::table('staffs')
    //              ->join('users','staffs.user_id','=','users.user_id')
    //              ->select('staffs.*','users.*')
    //              ->where('users.position', '=', 'Dean')
    //              ->where('staffs.faculty_id','=',$faculty_id)
    //              ->get();

    $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $action->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

    $all_ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $action->course_id)
                    ->orderBy('ass_final.fx_id')
                    ->get();

    $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $action->course_id)
                  ->get();

    $weightage = 0;
    foreach($ass_final as $row){
      $weightage = $weightage+$row->coursework;
    }


    $phpWord = new \PhpOffice\PhpWord\PhpWord();
      // New section
      $section = $phpWord->addSection(array('marginLeft' => 700, 'marginRight' => 700,'marginTop' => 1000, 'marginBottom' => 1000));
      $header = $section->addHeader();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black', 'cellMargin' => 10);
      $phpWord->addTableStyle('header', $styleTable);
      $table = $header->addTable('header');
      $cellRowSpan = array('vMerge' => 'restart','valign' => 'center');
      $cellRowContinue = array('vMerge' => 'continue','valign' => 'center');
      $cellColSpan = array('gridSpan' => 2);
      $noSpaceAndCenter = array('spaceAfter' => 0,'align'=>'center');
      $noSpaceAndRight = array('spaceAfter' => 0,'align'=>'right');
      $table->addRow(1);
      $table->addCell(4000, $cellRowSpan)->addImage('image/logo.png', array('width' => 132, 'height' => 40),$noSpaceAndCenter);
      $table->addCell(5000, $cellRowSpan)->addText("",$noSpaceAndCenter);
      $table->addCell(2200)->addText("Doc. No.",null,$noSpaceAndCenter);
      $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(2200)->addText("Rev. No.",null,$noSpaceAndCenter);
      $table->addCell(2500)->addText("00",null,$noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('INTERNAL/EXTERNAL MODERATION OF FINAL EXAMINATION PAPER'),null,$noSpaceAndCenter);
      $table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
      $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
      $table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

      $textrun = $header->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $FA_full_title = $section->addText('INTERNAL/EXTERNAL MODERATION OF FINAL EXAMINATION QUESTION PAPER <w:br/> FINAL EXAMINATION / RESIT EXAMINATION',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part A : Course Information',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('Course Table', $styleTable);
      $course_table = $section->addTable('Course Table');
      $styleCell = array('valign' => 'center');
      $cellColSpan = array('gridSpan' => '3','valign' => 'center');
      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Faculty : ', null, $noSpaceAndLeft);
      $course_table->addCell(9000,$cellColSpan)->addText($course[0]->faculty_name, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Subject Code : ', null, $noSpaceAndLeft);
      $course_table->addCell(9000,$cellColSpan)->addText($course[0]->subject_code, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Subject Name : ', null, $noSpaceAndLeft);
      $course_table->addCell(9000,$cellColSpan)->addText($course[0]->subject_name, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Lecturer Name : ', null, $noSpaceAndLeft);
      $course_table->addCell(9000,$cellColSpan)->addText($course[0]->name, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Internal Moderator : ', null, $noSpaceAndLeft);
      $course_table->addCell(9000,$cellColSpan)->addText($Moderator[0]->name, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Semester : ', null, $noSpaceAndLeft);
      $course_table->addCell(9000,$cellColSpan)->addText($course[0]->semester, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(3000,$styleCell)->addText('Academic Year : ', null, $noSpaceAndLeft);
      $course_table->addCell(3000,$styleCell)->addText('20'.$course[0]->year, null, $noSpaceAndLeft);
      $course_table->addCell(3000,$styleCell)->addText('% Weightage of <w:br/> Continuous Assessment', null, $noSpaceAndCenter);
      $course_table->addCell(3000,$styleCell)->addText($weightage.'%', null, $noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part B : Distribution of topic and ( CLO )',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);


      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('topic Table', $styleTable);
      $table = $section->addTable('topic Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc');
      $table->addRow(1);
      $table->addCell(3000,$styleCellTH)->addText('Question No.<w:br/>*(New) is created after moderation*',$fontStyle, $noSpaceAndCenter);
      $table->addCell(4000,$styleCellTH)->addText("Topic(s) covered", $fontStyle, $noSpaceAndCenter);
      $table->addCell(6000,$styleCellTH)->addText('Course Learning Outcome (s) covered',$fontStyle, $noSpaceAndCenter);
      $table->addCell(3000,$styleCellTH)->addText("Bloom's Taxanomy Level*", $fontStyle, $noSpaceAndCenter);

      foreach($all_ass_final as $row){
        $get = false;
        $array = array();
        $full_suggest = explode('///NextAss///',$action->suggest);
        for($n = 0;$n<=(count($full_suggest)-1);$n++){
          $getFxId = explode('<???>',$full_suggest[$n]);
          if($getFxId[0]==$row->fx_id){
            $get = true;
          }
          array_push($array,$getFxId[0]);
        }
        if((($row->fx_id>=max($array))&&($row->status!="Remove"))||($get == true)){
          $table->addRow(1);
          $text = "";
          if($row->status=="Remove"){
            $text = " (Removed)";
          }
          if($row->fx_id>max($array)){
            $text = " (New)";
          }

          $table->addCell(3000,$styleCell)->addText($row->assessment_name.$text,Null, $noSpaceAndCenter);
          $table->addCell(3000,$styleCell)->addText($row->topic,Null, $noSpaceAndCenter);
          $table->addCell(3000,$styleCell)->addText($row->CLO,Null, $noSpaceAndCenter);
          $CLO_sel = explode(',',$row->CLO);
          $domain_level = "";
          for($i = 0; $i<=count($CLO_sel)-1;$i++){
            $num = 1;
            foreach($TP_Ass as $row_ass){
              if(('CLO'.$num) == $CLO_sel[$i]){
                $domain_level .= $row_ass->domain_level.',';
              }
              $num++;
            }
          }
          $table->addCell(3000,$styleCell)->addText($domain_level,Null, $noSpaceAndCenter);
        }
      }

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('CLO Table', $styleTable);
      $table = $section->addTable('CLO Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc');
      $num = 1;
      foreach ($TP_Ass as $row_ass){
        $table->addRow(1);
        $table->addCell(3000,$styleCell)->addText('CLO '.$num,Null, $noSpaceAndCenter);
        $table->addCell(9000,$styleCell)->addText($row_ass->CLO."<w:br/>( ".$row_ass->domain_level.' , '.$row_ass->PO." ) ",Null, $noSpaceAndLeft);
        $num++;
      }

      $section->addPageBreak();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part C : Indicate the degree to which moderator agree or disagree',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);


      $degree = explode('///',$action->degree);
      $styleCell = array('valign' => 'center');
      $styleThCell = array('valign' => 'center','bgColor' => 'cccccc');
      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $phpWord->addTableStyle('degree Table', $styleTable);
      $table = $section->addTable('degree Table');
      $cellRowSpan = array('vMerge' => 'restart','valign' => 'center','bgColor' => 'cccccc');
      $cellRowContinue = array('vMerge' => 'continue','valign' => 'center','bgColor' => 'cccccc');
      $cellColSpan = array('gridSpan' => 7,'valign' => 'center');
      $table->addRow(1);
      $table->addCell(1000,$cellRowSpan)->addText('No',$fontStyle, $noSpaceAndCenter);
      $table->addCell(6000,$cellRowSpan)->addText("", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("5", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("4", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("3", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("2", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("1", $fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$cellRowContinue);
      $table->addCell(6000,$cellRowContinue);
      $table->addCell(1000,$styleThCell)->addText("Strongly <w:br/> Agree", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Agree", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Neutral", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Disagree", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Strongly Disagree", $fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$cellColSpan)->addText('QUESTION PAPER',$fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('1',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions are within the scope of the course syllabus and are aligned to the mapped CLOs and PLOs",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[0]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('2',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions are arranged according to complexity from lower difficult level to higher difficulty level",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[1]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('3',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("None of the questions in the examination questions paper are overlap.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[2]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('4',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Question are free from factual errors.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[3]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('5',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions are free from racial/ethnic, religious, sexual and political bias and other sensitive issues.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[4]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('6',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Optional questions (if any) are equivalent in terms of CLO and marks awarded (if applicable).",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[5]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('7',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions and the descriptions are simple and clear.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[6]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('8',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Scientific / technical terminologies are relevant to the course.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[7]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('9',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Labels and descriptions used for diagrams, tables and figures are clear and consistent.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[8]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$cellColSpan)->addText('MARKING SCHEME',$fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('1',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Marks(s) stated in the marking scheme are based on the examination paper set.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[9]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('2',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Answer for each question is correct and appropriate to CLOs.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[10]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('3',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Total marks for each question and/or section for the whole examination paper correctly calculated and stated.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[11]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $section->addPageBreak();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part D : Suggestion for improvement',array('bold' => true),$noSpaceAndCenter);

      foreach($all_ass_final as $row){
        $suggest_list = array("");
        $percentage = array("","","");
        $full_suggest = explode('///NextAss///',$action->suggest);
        for($n = 0;$n<=(count($full_suggest)-1);$n++){
            $getFxId = explode('<???>',$full_suggest[$n]);
            if($getFxId[0]==$row->fx_id){
              $suggest_list = explode('%-PER-%',$getFxId[1]);
              $percentage = explode(',',$suggest_list[1]);
            }
        }
        if($suggest_list[0]!=""){
          $textrun = $section->addTextRun();
          $textrun->addText("",null,$noSpaceAndCenter);
          $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
          $fontStyle = array('bold' => true);
          $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
          $phpWord->addTableStyle('suggest Table', $styleTable);
          $table = $section->addTable('suggest Table');
          $styleCell = array('valign' => 'center');
          $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2);
          $cellColSpan = array('gridSpan' => 2,'valign' => 'center');
          $table->addRow(1);
          $text = "";
          if($row->status=="Remove"){
            $text = " (Removed)";
          }
          $table->addCell(12000,$styleCellTH)->addText($row->assessment_name.$text,$fontStyle, $noSpaceAndLeft);
          $table->addRow(1);
          $suggest = $table->addCell(12000,$cellColSpan);
          $html = str_replace("<br>","<br/>",$suggest_list[0]);
          \PhpOffice\PhpWord\Shared\Html::addHtml($suggest,$html,false);

          $table->addRow(1);
          $table->addCell(9000,$styleCell)->addText('Percentage of work involving remembering and understanding %',Null, $noSpaceAndLeft);
          $table->addCell(3000,$styleCell)->addText($percentage[0].'%',Null, $noSpaceAndRight);

          $table->addRow(1);
          $table->addCell(9000,$styleCell)->addText('Percentage of work involving application &amp; analysis %',Null, $noSpaceAndLeft);
          $table->addCell(3000,$styleCell)->addText($percentage[1].'%',Null, $noSpaceAndRight);

          $table->addRow(1);
          $table->addCell(9000,$styleCell)->addText('Percentage of work involving evaluation and creation %',Null, $noSpaceAndLeft);
          $table->addCell(3000,$styleCell)->addText($percentage[2].'%',Null, $noSpaceAndRight);

          $table->addRow(1);
          $table->addCell(9000,$styleCell)->addText('Total',Null, $noSpaceAndLeft);
          $table->addCell(3000,$styleCell)->addText('100%',Null, $noSpaceAndRight);
        }
      }

      $section->addPageBreak();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part E : Any Other Feedback',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);
      $styleTable = array('borderBottomSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('feedback Table', $styleTable);
      $table = $section->addTable('feedback Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $feedback = $table->addCell(12000);
      $html = str_replace("<br>","<br/>",$action->feedback);
      \PhpOffice\PhpWord\Shared\Html::addHtml($feedback,'<p><b>Feedback: </b></p>'.$html,false);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);
      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('Sign Table', $styleTable);
      $table = $section->addTable('Sign Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $table->addCell(5000)->addText('Internal / External Moderator:',array('bold' => true),$noSpaceAndCenter);
      $table->addRow(1);
      if($action->moderator_date!=NULL){
        if($Moderator[0]->staff_sign!=NULL){
          $s_p = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
          $table->addCell(5000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
        }else{
          $table->addCell(5000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
        }
      }else{
          $table->addCell(5000,$styleCell)->addText("",Null,$noSpaceAndCenter);
      }
      $table->addRow(1);
      $table->addCell(5000)->addText('Name: '.$Moderator[0]->name, null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(5000)->addText('Date: '.$action->moderator_date, null, $noSpaceAndLeft);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('Self Table', $styleTable);
      $table = $section->addTable('Self Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6,'borderTopSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderTopSize' => 6,'borderRightSize' => 6,'gridSpan' => 2))->addText('Self-declaration',$fontStyle,$noSpaceAndLeft);
      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderRightSize' => 6,'gridSpan' => 2))->addText('I hereby declared that the Final Examination Question Paper has been moderated by Internal / External Moderator and I have corrected all the amendments according to the comments from Internal / External Moderator.',Null,$noSpaceAndLeft);

      $table->addRow(1000);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      if($action->self_date!=NULL){
        if($action->self_declaration=="Yes"){
          if($course[0]->staff_sign!=NULL){
            $s_p = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
            $table->addCell(5000,array('borderBottomSize' => 6))->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
          }else{
            $table->addCell(5000,array('borderBottomSize' => 6))->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
          }
        }else{
          $table->addCell(5000,array('borderBottomSize' => 6))->addText("",Null,$noSpaceAndCenter);
        }
      }else{
        $table->addCell(5000,array('borderBottomSize' => 6))->addText("",Null,$noSpaceAndCenter);
      }
      // $table->addCell(5000,array('borderBottomSize' => 6))->addText(' ', null, $noSpaceAndLeft);
      $table->addCell(7000,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(5000)->addText('Name: '.$course[0]->name, null, $noSpaceAndLeft);
      $table->addCell(7000,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(5000)->addText('Date: '.$action->self_date, null, $noSpaceAndLeft);
      $table->addCell(7000,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(12000,array('borderBottomSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 3))->addText('',$fontStyle,$noSpaceAndLeft);

      $section->addPageBreak();

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('A_V Table', $styleTable);
      $table = $section->addTable('A_V Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $table->addCell(12000,array('borderTopSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 5))->addText('Approval and Verification',$fontStyle,$noSpaceAndCenter);
      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderRightSize' => 6,'gridSpan' => 4))->addText('Comments from '.$verified_by[0]->position.' (if any): ',Null,$noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);

      $remark_verify = $table->addCell(12000,array('borderBottomSize' => 6,'gridSpan' => 3));
      $html = str_replace("<br>","<br/>",$action->remarks);
      \PhpOffice\PhpWord\Shared\Html::addHtml($remark_verify,$html,false);

      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(1000,array('gridSpan' => 2))->addText('Signature if '.$verified_by[0]->position, null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      if($action->verified_date!=NULL){
        if($verified_by[0]->staff_sign!=NULL){
          $s_p = storage_path('/private/staffSign/'.$verified_by[0]->staff_sign);
          $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
        }else{
          $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addText($verified_by[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
        }
      }else{
          $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addText("",Null,$noSpaceAndCenter);
      }
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Name: '.$verified_by[0]->name, null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Date: '.$action->verified_date, null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(12000,array('borderBottomSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 5))->addText('',$fontStyle,$noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderRightSize' => 6,'gridSpan' => 4))->addText('Comments from Dean (if any): ',Null,$noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $remark_approve = $table->addCell(12000,array('borderBottomSize' => 6,'gridSpan' => 3));
      $html = str_replace("<br>","<br/>",$action->remarks_dean);
      \PhpOffice\PhpWord\Shared\Html::addHtml($remark_approve,$html,false);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      if($action->approved_date!=NULL){
        $table->addCell(1000,array('borderSize' => 6))->addText('Y', array("color"=>"green",'bold' => true), $noSpaceAndCenter);
      }else{
         $table->addCell(1000,array('borderSize' => 6))->addText('', array("color"=>"green",'bold' => true), $noSpaceAndCenter);
      }
      $table->addCell(3000)->addText('Approval For Printing', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(1000,array('gridSpan' => 2))->addText('Signature if Dean', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      if($action->approved_date!=NULL){
        if($approved_by[0]->staff_sign!=NULL){
          $s_p = storage_path('/private/staffSign/'.$approved_by[0]->staff_sign);
          $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
        }else{
          $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addText($approved_by[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
        }
      }else{
          $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addText("",Null,$noSpaceAndCenter);
      }
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Name: '.$approved_by[0]->name, null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Date: '.$action->approved_date, null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(12000,array('borderBottomSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 5))->addText('',$fontStyle,$noSpaceAndLeft);

      $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
      $objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
      return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
  }

  public function createPreviousAss($id){

      $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('course_id', '=', $id)
                 ->get();

      $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

      $tp = DB::table('teaching_plan')
                  ->join('plan_topics','teaching_plan.tp_id','=','plan_topics.tp_id')
                  ->select('teaching_plan.*','plan_topics.*')
                  ->where('teaching_plan.course_id', '=', $id)
                  ->groupBy('plan_topics.lecture_topic')
                  ->get();

      if(count($TP_Ass)<=0||count($tp)<=0){
        return redirect()->back()->with('Failed',"The Teaching Plan(Assessment Method and Weekly Plan) are related with final assessment list. So, Please fill in them first.");
      }

      if($course[0]->semester =='A'){
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
                 ->where('courses.course_id', '!=', $id)
                 ->where('courses.status', '=', 'Active')
                 ->where('semesters.semester','=','A')
                 ->orderByDesc('semesters.semester_name')
                 ->get();
        $failed = "The course have not yet open in short semester. Please write down the Final Assessment list for this course.";
      }else{
        $previous = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
                 ->where('courses.course_id', '!=', $id)
                 ->where('courses.status', '=', 'Active')
                 ->where('semesters.semester','!=',"A")
                 ->orderByDesc('semesters.semester_name')
                 ->get();
        $failed = "The course have not yet open in long semester. Please write down the Final Assessment list for this course.";
      }

      if(count($previous)>0){
            $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $previous[0]->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

            if(count($ass_final)>0){
                $removeActive = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

                foreach($removeActive as $ra){
                    $rm = AssFinal::where('fx_id', '=', $ra->fx_id)->firstOrFail();
                    $rm_list = AssessmentFinal::where('fx_id','=',$ra->fx_id)->update(['status' => 'Remove']);
                    $rm->status  = "Remove";
                    $rm->save();
                }

                foreach($ass_final as $row){
                    $final = new AssFinal([
                        'course_id'         =>  $id,
                        'topic'             =>  $row->topic,
                        'CLO'               =>  $row->CLO,
                        'coursemark'        =>  $row->coursemark,
                        'coursework'        =>  $row->coursework,
                        'assessment_name'   =>  $row->assessment_name,
                        'status'            =>  'Active',
                    ]);
                    $final->save();
                }
                return redirect()->back()->with('success','Final Assessment List Inserted Successfully');
            }else{
              return redirect()->back()->with('Failed',"Your last semester of Final Assessment List is empty.");
            }
        }else{
            return redirect()->back()->with('Failed',$failed);
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
