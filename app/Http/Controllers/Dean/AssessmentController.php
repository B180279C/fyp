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
use App\Assessments;
use App\AssessmentList;
use App\Imports\syllabusRead;
use ZipArchive;
use File;
use App\ActionCA_V_A;

class AssessmentController extends Controller
{
    public function viewAssessment($id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $action = DB::table('actionCA_v_a')
                  ->select('actionCA_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('actionCA_id')
                  ->get();

        $moderator_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
        $moderator_person_name = User::where('user_id', '=', $moderator_by->user_id)->firstOrFail();

        $verified_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        // $verified_by = DB::table('staffs')
        //          ->join('users','staffs.user_id','=','users.user_id')
        //          ->select('staffs.*','users.*')
        //          ->where('users.position', '=', 'HoD')
        //          ->where('staffs.department_id','=',$department_id)
        //          ->get();

        if(count($course)>0){
            return view('dean.Assessment.viewAssessment',compact('course','assessments','action','moderator_person_name','verified_person_name'));
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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        if(count($course)>0){
            $path = storage_path('private/syllabus/'.$course[0]->syllabus);
            $array = (new syllabusRead)->toArray($path);
            return response()->json([$array[0],$assessments]);
            // return response()->json($array[0]);
        }else{
            return redirect()->back();
        }      
    }

    public function create_question($id,$coursework,$question)
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
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $mark = 0;
        foreach ($assessments as $row){
            $mark = $mark+$row->coursework;
        }

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

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $sample_stored = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->where('sample_stored','=','own')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.createQuestion',compact('course','mark','question','assessments','previous_semester','group_assessments','TP_Ass','coursework','sample_stored'));
        }else{
            return redirect()->back();
        }
    }

    public function assessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();

        $checkImageASSID = AssessmentList::where('ass_document', '=', $image_name)->firstOrFail();
        $ass_id = $checkImageASSID->ass_id;

        $checkCourseId = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $checkCourseId->course_id;

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

    public function openNewAssessment(Request $request){
        $course_id    = $request->get('course_id');
        $ass_name     = $request->get('assessment_name');
        $assessment   = $request->get('assessment');
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
        $assessment = new Assessments([
            'course_id'         =>  $course_id,
            'assessment'        =>  $assessment,
            'assessment_name'   =>  $ass_name,
            'CLO'               =>  $CLO_List,
            'coursemark'        =>  $total,
            'coursework'        =>  $coursework,
            'status'            =>  'Active',
        ]);
        $assessment->save();

        return redirect()->back()->with('success','New Assessment Added Successfully');
    }

    public function AssessmentNameEdit(Request $request){
        $ass_id = $request->get('value');
        $folder = Assessments::find($ass_id);
        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id','=',$folder->course_id)
                    ->where('assessment','=',$folder->assessment)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $folder->course_id)
                  ->get();       

        return [$folder,$assessments,$TP_Ass];
    }

    public function updateAssessmentName(Request $request){
        $ass_id   = $request->get('ass_id');
        $coursework = $request->get('coursework');
        $CLO = array();
        $CLO = $request->get('CLO');
        $CLO_ALL = $request->get('CLO_ALL');
        $CLO_List = "";
        if($CLO!=null){  
          foreach($CLO as $value){
            $CLO_List .= $value.',';
          }
        }
        $assessment = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $assessment->assessment_name  = $request->get('assessment_name');
        $assessment->CLO  = $CLO_List;
        $assessment->coursemark  = $request->get('total');
        $assessment->coursework  = $request->get('coursework');

        $assessment->save();
        return redirect()->back()->with('success','Edit Assessment Detail Successfully');
    }

    public function assessment_list_view($ass_id){
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $question = $assessments->assessment;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessments->course_id)
                 ->get();

        $group_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('ass_id', '=', $ass_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('ass_type')
                    ->get();

        $assessment_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('ass_id', '=', $ass_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessment_list.ass_type')
                    ->orderBy('assessment_list.ass_name')
                    ->orderBy('assessment_list.ass_li_id')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.AssessmentListView', compact('course','assessments','question','group_list','assessment_list'));
        }else{
            return redirect()->back();
        }
    }

    public function removeActive($id){
        $assessment = Assessments::where('ass_id', '=', $id)->firstOrFail();
        $assessment_list = AssessmentList::where('ass_id','=',$id)->update(['status' => 'Remove']);
        $assessment->status  = "Remove";
        $assessment->save();
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function removeActiveList($id){
        $assessment_list = AssessmentList::where('ass_li_id','=',$id)->update(['status' => 'Remove']);
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
                        $assessments = new AssessmentList([
                            'ass_id'                 =>  $request->get('ass_id'),
                            'ass_name'               =>  $name,
                            'ass_type'               =>  $request->get('ass_type'),
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

    public function view_wholePaper($ass_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessments->course_id)
                 ->get();

        $question = $assessments->assessment;

        $assessment_list = DB::table('assessment_list')
                    ->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessment_list.*','courses.*','semesters.*')
                    ->where('assessment_list.ass_id', '=', $ass_id)
                    ->where('assessment_list.status', '=', 'Active')
                    ->orderBy('assessment_list.ass_id')
                    ->orderBy('assessment_list.ass_type')
                    ->orderBy('assessment_list.ass_name')
                    ->get();

        if(count($course)>0){
            return view('dean.Assessment.viewWholePaper', compact('assessments','assessment_list','question'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadFiles($ass_li_id){

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment_list = AssessmentList::where('ass_li_id', '=', $ass_li_id)->firstOrFail();

        $ass_id = $assessment_list->ass_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $question = $assessments->assessment_name;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessments->course_id)
                 ->get();

        if(count($course)>0){
            $ext = "";
            if($assessment_list->ass_document!=""){
                $ext = explode(".", $assessment_list->ass_document);
            }

            return Storage::disk('private')->download('Assessment/'.$assessment_list->ass_document, $question."_".$assessment_list->ass_type."_".$assessment_list->ass_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    

    public function searchAssessmentList(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');
        $question      = $request->get('question');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }

        $result = "";
        if($value!=""){
            $assessments = DB::table('assessments')
                        ->select('assessments.*')
                        ->where('assessments.assessment_name','LIKE','%'.$value.'%')
                        ->where('assessments.assessment', '=', $question)
                        ->where('assessments.status', '=', 'Active')
                        ->where('assessments.course_id','=',$course_id)
                        ->orderBy('assessments.assessment_name')
                        ->orderBy('assessments.ass_id')
                        ->get();
            if(count($assessments)>0) {
                foreach ($assessments as $row) {
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-8 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->ass_id.'" value="'.$row->ass_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-4" id="course_action_two">';
                    $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                        <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                    $result .= '</div>';
                    $result .= '</div> ';
                }
            }else{
                $result .= '<div class="col-md-12" style="position:relative;top:10px;">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $course_id)
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->orderBy('assessments.ass_id')
                    ->get();

            if(count($assessments)>0) {
                foreach ($assessments as $row) {
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-8 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->ass_id.'" value="'.$row->ass_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-4" id="course_action_two">';
                    $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                        <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
        $ass_id        = $request->get('ass_id');

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $assessments->course_id)
                 ->get();

        $result = "";
        if($value!=""){
            $group_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('assessment_list.ass_id', '=', $ass_id)
                    ->Where(function($query) use ($value) {
                        $query->orWhere('assessment_list.ass_name','LIKE','%'.$value.'%')
                            ->orWhere('assessment_list.ass_word','LIKE','%'.$value.'%');
                    })
                    ->where('assessment_list.status', '=', 'Active')
                    ->groupBy('assessment_list.ass_type')
                    ->get();

            $assessment_list = DB::table('assessment_list')
                        ->select('assessment_list.*')
                        ->where('assessment_list.ass_id', '=', $ass_id)
                        ->where('assessment_list.status', '=', 'Active')
                        ->Where(function($query) use ($value) {
                            $query->orWhere('assessment_list.ass_name','LIKE','%'.$value.'%')
                                ->orWhere('assessment_list.ass_word','LIKE','%'.$value.'%');
                        })
                        ->orderBy('assessment_list.ass_type')
                        ->orderBy('assessment_list.ass_name')
                        ->orderBy('assessment_list.ass_li_id')
                        ->get();
            $i=0;
            if(count($group_list)>0) {
                foreach($group_list as $row_group){
                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<div class="col-12 row" style="padding:10px;margin: 0px;">';
                    $result .= '<div class="checkbox_group_style">';
                    $result .= '<input type="checkbox" id="group_'.$row_group->ass_type.'" class="group_checkbox">';
                    $result .= '</div>';
                    $result .= '<h5 class="group plus" id="'.$i.'">'.$row_group->ass_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_list as $row){
                        if($row_group->ass_type == $row->ass_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_li_id.'_'.$row->ass_type.'" class="group_'.$row_group->ass_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='.$character."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                          <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
            $group_list = DB::table('assessment_list')
                    ->select('assessment_list.*')
                    ->where('ass_id', '=', $ass_id)
                    ->where('status', '=', 'Active')
                    ->groupBy('ass_type')
                    ->get();

            $assessment_list = DB::table('assessment_list')
                        ->select('assessment_list.*')
                        ->where('ass_id', '=', $ass_id)
                        ->where('status', '=', 'Active')
                        ->orderBy('assessment_list.ass_type')
                        ->orderBy('assessment_list.ass_name')
                        ->orderBy('assessment_list.ass_li_id')
                        ->get();
            $i = 0;
            if(count($group_list)>0) {
                foreach($group_list as $row_group){
                    $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .= '<div class="col-12 row" style="padding:10px;margin: 0px;">';
                    $result .= '<div class="checkbox_group_style">';
                    $result .= '<input type="checkbox" id="group_'.$row_group->ass_type.'" class="group_checkbox">';
                    $result .= '</div>';
                    $result .= '<h5 class="group plus" id="'.$i.'">'.$row_group->ass_type.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .= '</div>';
                    $result .= '<div id="assessment_list_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">';
                    foreach($assessment_list as $row){
                        if($row_group->ass_type == $row->ass_type){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_li_id.'_'.$row->ass_type.'" class="group_'.$row_group->ass_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="'.$character.'/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='.$character."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                          <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
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
            $question = $string[1];
        }
        
        $subjects = DB::table('subjects')
                    ->join('courses','courses.subject_id','=','subjects.subject_id')
                    ->select('courses.*','subjects.*')
                    ->where('courses.course_id', '=', $course_id)
                    ->get();

        $name = "Assessment ( ".$subjects[0]->subject_code." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment/'));

        if($download == "checked"){
            for($i=1;$i<(count($string)-1);$i++){
                $checkASSID = Assessments::where('ass_id', '=', $string[$i])->firstOrFail();
                $zip->addEmptyDir($checkASSID->assessment_name);

                $result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $checkASSID->ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();

                foreach($result_list as $rl_row){
                    if($rl_row->ass_type=="Question"){
                        $zip->addEmptyDir($checkASSID->assessment_name."/Question");
                    }else{
                        $zip->addEmptyDir($checkASSID->assessment_name."/Solution"); 
                    }
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->ass_document == $relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->ass_type=="Question"){
                                $zip->addFile($value,$checkASSID->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$checkASSID->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
                            } 
                        }
                    }
                }
            }
        }else{
            $assessments = DB::table('assessments')
                     ->select('assessments.*')
                     ->where('course_id', '=', $course_id)
                     ->where('assessment','=',$question)
                     ->where('status','=','Active')
                     ->get();

            foreach($assessments as $ass_row){
                $zip->addEmptyDir($ass_row->assessment_name);
                $result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $ass_row->ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();
                foreach($result_list as $rl_row){
                    if($rl_row->ass_type=="Question"){
                        $zip->addEmptyDir($ass_row->assessment_name."/Question");
                    }else{
                        $zip->addEmptyDir($ass_row->assessment_name."/Solution"); 
                    }
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($rl_row->ass_document == $relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($rl_row->ass_type=="Question"){
                                $zip->addFile($value,$ass_row->assessment_name."/Question/".$rl_row->ass_name.'.'.$ext[1]);
                            }else{
                                $zip->addFile($value,$ass_row->assessment_name."/Solution/".$rl_row->ass_name.'.'.$ext[1]);
                            } 
                        }
                    }
                }
            }
        }
        $zip->close();
        if($this->checkCoursePerson($course_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Assessment/Zip_Files/'.$name.'.zip');
            return redirect()->back();
        }
    }

    public function zipFileDownload($ass_id,$download)
    {
        if($download == "checked"){
            $string = explode('---',$ass_id);
            $f_ass_id = $string[0];
        }else{
            $f_ass_id = $ass_id;
        }

        $assessments = Assessments::where('ass_id', '=', $f_ass_id)->firstOrFail();
        $course_id = $assessments->course_id;

        $subjects = DB::table('subjects')
                        ->join('courses','courses.subject_id','=','subjects.subject_id')
                        ->select('courses.*','subjects.*')
                        ->where('courses.course_id', '=', $course_id)
                        ->get();

        $name = $assessments->assessment_name." ( ".$subjects[0]->subject_code." )";
        $zip = new ZipArchive;
        $fileName = storage_path('private/Assessment/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/Assessment/'));
        
        if($download == "checked"){
            for($i=1;$i<(count($string)-1);$i++){
                $studentId_By = explode('_',$string[$i]);
                $zip->addEmptyDir($studentId_By[1]);
                $assessment_list = AssessmentList::where('ass_li_id', '=', $studentId_By[0])->firstOrFail();
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($assessment_list->ass_document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        $zip->addFile($value,$studentId_By[1]."/".$assessment_list->ass_name.'.'.$ext[1]);
                    }
                }
            }
        }else{
            $result_list = DB::table('assessment_list')
                         ->select('assessment_list.*')
                         ->where('assessment_list.ass_id', '=', $ass_id)
                         ->where('assessment_list.status','=','Active')
                         ->get();

            foreach($result_list as $rl_row){
                if($rl_row->ass_type=="Question"){
                    $zip->addEmptyDir("Question");
                }else{
                    $zip->addEmptyDir("Solution"); 
                }
                foreach ($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    if($rl_row->ass_document == $relativeNameInZipFile){
                        $ext = explode('.',$relativeNameInZipFile);
                        if($rl_row->ass_type=="Question"){
                            $zip->addFile($value,"Question/".$rl_row->ass_name.'.'.$ext[1]);
                        }else{
                            $zip->addFile($value,"Solution/".$rl_row->ass_name.'.'.$ext[1]);
                        } 
                    }
                }
            } 
        }
        $zip->close();
        if($this->checkCoursePerson($course_id)==true){
            return response()->download($fileName)->deleteFileAfterSend(true);
        }else{
            Storage::disk('private')->delete('/Assessment/Zip_Files/'.$name.'.zip');
            return redirect()->back();
        }
    }
    public function AssessmentSubmitAction($id)
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
          $action = new ActionCA_V_A([
            'course_id'   => $id,
            'status'      => "Waiting For Moderation",
            'for_who'     => "Moderator",
          ]);
          $action->save();
          return redirect()->back()->with('success','Continuous Assessment Submitted to Moderator Successfully');
        }else{
          return redirect()->back();
        }
    }

    public function SubmitSelf_D_Form(Request $request)
    {
        $actionCA_id = $request->get('actionCA_id');
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
            $action = ActionCA_V_A::where('actionCA_id', '=', $actionCA_id)->firstOrFail();
            $action->status  = 'Waiting For Verified';
            $action->for_who = 'HOD';
            $action->self_declaration = $status;
            $action->save();
            return redirect()->back()->with('success','Continuous Assessment Submitted to HOD Successfully');
        }else{
            return redirect()->back();
        }
    }

    public function ModerationFormReport($actionCA_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        $action = ActionCA_V_A::where('actionCA_id', '=', $actionCA_id)->firstOrFail();

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

        // $verified_by = DB::table('staffs')
        //          ->join('users','staffs.user_id','=','users.user_id')
        //          ->select('staffs.*','users.*')
        //          ->where('users.position', '=', 'HoD')
        //          ->where('staffs.department_id','=',$department_id)
        //          ->get();

        $verified_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id', '=', $course[0]->verified_by)
                 ->get();

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $all_assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action->course_id)
                    ->orderBy('assessments.ass_id')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $action->course_id)
                  ->get();

        $weightage = 0;
        foreach($assessments as $row){
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
        $table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('COUTINUOUS ASSESSMENT MODERATION FORM'),null,$noSpaceAndCenter);
        $table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
        $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

        $table->addRow(1);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
        $table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

        $textrun = $header->addTextRun();
        $textrun->addText("",null,$noSpaceAndCenter);

        $CA_full_title = $section->addText('INTERNAL CONTINUOUS ASSESSMENT MODERATION FORM',array('bold' => true),$noSpaceAndCenter);

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
        $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part B : ( CLO ) targeted in the Assessment Method',array('bold' => true),$noSpaceAndCenter);
        $all_assessments_count = 0;
        foreach($all_assessments as $row){
            $get = false;
            $AccOrRec_list = explode('///',$action->AccOrRec);
            $array = array();
            for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                $get = true;
                }
                array_push($array,$action_ass_id[2]);
            }
            if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                $all_assessments_count++;
            }
        }

        $textrun = $section->addTextRun();
        $textrun->addText("",null,$noSpaceAndCenter);
        $styleCell = array('valign' => 'center');
        $styleThCell = array('valign' => 'center','bgColor' => 'cccccc');
        $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
        $fontStyle = array('bold' => true);
        $phpWord->addTableStyle('Assessment Method Table', $styleTable);
        $table = $section->addTable('Assessment Method Table');
        $cellRowSpan = array('vMerge' => 'restart','valign' => 'center','bgColor' => 'cccccc');
        $cellRowContinue = array('vMerge' => 'continue','valign' => 'center','bgColor' => 'cccccc');
        $cellColSpan = array('gridSpan' => $all_assessments_count,'valign' => 'center','bgColor' => 'cccccc');
        $table->addRow(1);
        $table->addCell(6000,$cellRowSpan)->addText('Course Learning Outcome covered',$fontStyle, $noSpaceAndCenter);
        $table->addCell(6000,$cellColSpan)->addText("Continuous Assessment<w:br/>*(New) is created after moderation*", $fontStyle, $noSpaceAndCenter);

        $table->addRow(1);
        $table->addCell(6000,$cellRowContinue);
        foreach($all_assessments as $row){
            $get = false;
            $AccOrRec_list = explode('///',$action->AccOrRec);
            $array = array();
            for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                $get = true;
                }
                array_push($array,$action_ass_id[2]);
            }
            if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                $text = "";
                if($row->status=="Remove"){
                    $text = " (Removed)";
                }
                if($row->ass_id>max($array)){
                    $text = " (New)";
                }
                $table->addCell((6000/$all_assessments_count),$styleThCell)->addText($row->assessment_name.$text,$fontStyle, $noSpaceAndCenter);
            }
        }
        $num = 1;
        foreach($TP_Ass as $row_tp){
            $table->addRow(1);
            $table->addCell(6000,$styleCell)->addText('CLO '.$num." : ".$row_tp->CLO."<w:br/>( ".$row_tp->domain_level.' , '.$row_tp->PO." ) ",null, $noSpaceAndLeft);
            foreach($all_assessments as $row){
                $get = false;
                $AccOrRec_list = explode('///',$action->AccOrRec);
                $array = array();
                for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                    $get = true;
                }
                array_push($array,$action_ass_id[2]);
                }
                $check = false;
                $CLO = $row->CLO;
                $CLO_sel = explode('///',$CLO);
                $CLO_List = explode(',',$CLO_sel[0]);
                for($i = 0;$i<=count($CLO_List)-1;$i++){
                    if($CLO_List[$i]==('CLO'.$num)){
                        $check = true;
                    }
               }
                if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                   if($check==true){
                    $table->addCell((6000/count($assessments)),$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                   }else{
                    $table->addCell((6000/count($assessments)),$styleCell)->addText('N', array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
                   }
               }
            }
            $num++;
        }

        $section->addPageBreak();

        $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
        $phpWord->addTableStyle('title', $styleTable);
        $title = $section->addTable('title');
        // $section->addTextBreak(1);
        $title->addRow();
        $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part C : Accepted Or Rectification',array('bold' => true),$noSpaceAndCenter);

        $textrun = $section->addTextRun();
        $textrun->addText("",null,$noSpaceAndCenter);

        $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
        $fontStyle = array('bold' => true);
        $phpWord->addTableStyle('AorR table', $styleTable);
        $table = $section->addTable('AorR table');
        $styleCell = array('valign' => 'center');
        $cellColSpan = array('gridSpan' => 2,'valign' => 'center','bgColor' => 'cccccc');
        $cellColSpan_NoColor = array('gridSpan' => 2,'valign' => 'center');
        $cellColSpan_HOD = array('gridSpan' => $all_assessments_count*2,'valign' => 'center');
        $table->addRow(1);
        $table->addCell(6000,$cellColSpan)->addText('Assessment<w:br/>*(New) is created after moderation*',array('bold' => true), $noSpaceAndRight);
        foreach($all_assessments as $row){
            $get = false;
            $AccOrRec_list = explode('///',$action->AccOrRec);
            $array = array();
            for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                $get = true;
                }
                array_push($array,$action_ass_id[2]);
            }
            if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                $text = "";
                if($row->status=="Remove"){
                    $text = " (Removed)";
                }
                if($row->ass_id>max($array)){
                    $text = " (New)";
                }
                $table->addCell((6000/$all_assessments_count),$cellColSpan)->addText($row->assessment_name.$text,$fontStyle, $noSpaceAndCenter);
            }
        }
        $table->addRow(1);
        $table->addCell(6000,$cellColSpan)->addText('% of Coursework',array('bold' => true), $noSpaceAndRight);
        foreach($all_assessments as $row){
            $get = false;
            $AccOrRec_list = explode('///',$action->AccOrRec);
            $array = array();
            for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                $get = true;
                }
                array_push($array,$action_ass_id[2]);
            }
            if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                $table->addCell((6000/$all_assessments_count),$cellColSpan)->addText($row->coursework."%",$fontStyle, $noSpaceAndCenter);
            }
        }
        $table->addRow(1);
        $table->addCell(500,$styleCell)->addText('',array('bold' => true), $noSpaceAndRight);
        $table->addCell(5500,$styleCell)->addText('A = Accepted &amp; R = Rectification',array('bold' => true), $noSpaceAndRight);
        foreach($all_assessments as $row){
            $get = false;
            $AccOrRec_list = explode('///',$action->AccOrRec);
            $array = array();
            for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                $get = true;
                }
                array_push($array,$action_ass_id[2]);
            }
            if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                $table->addCell((6000/($all_assessments_count)/2),$styleCell)->addText('A',$fontStyle, $noSpaceAndCenter);
                $table->addCell((6000/($all_assessments_count)/2),$styleCell)->addText('R',$fontStyle, $noSpaceAndCenter);
            }
        }

        $num = 1;
        foreach ($TP_Ass as $row_tp){
            $table->addRow(1);
            $table->addCell(500,$styleCell)->addText($num,array('bold' => true), $noSpaceAndCenter);
            $table->addCell(5500,$styleCell)->addText('CLO '.$num." : ".$row_tp->CLO."<w:br/>( ".$row_tp->domain_level.' , '.$row_tp->PO." ) ",null, $noSpaceAndLeft);
            foreach($all_assessments as $row){
                $check = false;
                $get = false;
                $Acc = false;
                $rec = false;
                $AccOrRec_list = explode('///',$action->AccOrRec);
                for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                  $AorR = explode('::',$AccOrRec_list[$m]);
                  $action_ass_id = explode('_',$AorR[0]);
                  if($action_ass_id[2]==$row->ass_id){
                    $get = true;
                  }
                  array_push($array,$action_ass_id[2]);
                  if($AorR[0]=="CLO_".$num."_".$row->ass_id){
                    $check = true;
                    if($AorR[1]=="A"){
                      $Acc = true;
                    }else{
                      $rec = true;
                    }
                  }
                }
                if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                    if($check==true){
                        if($Acc==true){
                            $table->addCell((6000/($all_assessments_count)/2),$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                            $table->addCell((6000/($all_assessments_count)/2),$styleCell)->addText('', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                        }
                        if($rec==true){
                            $table->addCell((6000/($all_assessments_count)/2),$styleCell)->addText('', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                            $table->addCell((6000/($all_assessments_count)/2),$styleCell)->addText('Y',array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
                        }
                    }else{
                        $table->addCell((6000/($all_assessments_count)/2),$cellColSpan)->addText('',null, $noSpaceAndCenter);
                    }
                }
            }
            $num++;
        }
        $table->addRow(1);
        $table->addCell(6000,$cellColSpan_NoColor)->addText('Signature of Internal Moderator',array('bold' => true), $noSpaceAndRight);
        foreach($all_assessments as $row){
            $get = false;
            $AccOrRec_list = explode('///',$action->AccOrRec);
            $array = array();
            for($m = 0;$m<(count($AccOrRec_list)-1);$m++){
                $AorR = explode('::',$AccOrRec_list[$m]);
                $action_ass_id = explode('_',$AorR[0]);
                if($action_ass_id[2]==$row->ass_id){
                $get = true;
                }
                array_push($array,$action_ass_id[2]);
            }
            if((($row->ass_id>=max($array))&&($row->status!="Remove"))||($get == true)){
                $table->addCell((6000/$all_assessments_count),$cellColSpan_NoColor)->addText('',$fontStyle, $noSpaceAndCenter);
            }
        }
        $table->addRow(1);
        $table->addCell(6000,$cellColSpan_NoColor)->addText('Verified of Head of Department',array('bold' => true), $noSpaceAndRight);
        $table->addCell(6000,$cellColSpan_HOD)->addText('',$fontStyle, $noSpaceAndCenter);

        $section->addPageBreak();

        $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
        $phpWord->addTableStyle('title', $styleTable);
        $title = $section->addTable('title');
        // $section->addTextBreak(1);
        $title->addRow();
        $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part D : Suggestion for improvement',array('bold' => true),$noSpaceAndCenter);

        foreach($all_assessments as $row){
            $suggest_list = "";
            $full_suggest = explode('///NextAss///',$action->suggest);
            for($n = 0;$n<=(count($full_suggest)-1);$n++){
                $getAssId = explode('<???>',$full_suggest[$n]);
                if($getAssId[0]==$row->ass_id){
                    $suggest_list = $getAssId[1];
                }
            }
            if($suggest_list!=""){
                $textrun = $section->addTextRun();
                $textrun->addText("",null,$noSpaceAndLeft);
                $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
                $fontStyle = array('bold' => true);
                $phpWord->addTableStyle($row->assessment_name.'table', $styleTable);
                $table = $section->addTable($row->assessment_name.'table');
                $styleCell = array('valign' => 'center');
                $table->addRow(1);
                $text = "";
                if($row->status=="Remove"){
                    $text = " (Removed)";
                } 
                $table->addCell(12000)->addText($row->assessment_name.$text,array('bold' => true),$noSpaceAndLeft);
                $table->addRow(1);
                $suggest = $table->addCell(12000);
                $html = str_replace("<br>","<br/>",$suggest_list);
                \PhpOffice\PhpWord\Shared\Html::addHtml($suggest,'Suggestion(s): '.$html,false);
            }
        }

        $textrun = $section->addTextRun();
        $textrun->addText("",null,$noSpaceAndLeft);

        $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
        $fontStyle = array('bold' => true);
        $phpWord->addTableStyle('Sign table', $styleTable);
        $table = $section->addTable('Sign table');
        $styleCell = array('valign' => 'center');

        $table->addRow(1);
        $table->addCell(6000)->addText('Internal Moderator', $fontStyle, $noSpaceAndCenter);
        $table->addCell(6000)->addText('Verified by Head Of Department', $fontStyle, $noSpaceAndCenter);

        $table->addRow(1);
        if($action->moderator_date!=NULL){
          if($Moderator[0]->staff_sign!=NULL){
            $s_p = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
            $table->addCell(6000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
          }else{
            $table->addCell(6000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
          }
        }else{
          $table->addCell(6000,$styleCell)->addText("",Null,$noSpaceAndCenter);
        }

        if($action->verified_date!=NULL){
          if($verified_by[0]->staff_sign!=NULL){
            $s_p = storage_path('/private/staffSign/'.$verified_by[0]->staff_sign);
            $table->addCell(6000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
          }else{
            $table->addCell(6000,$styleCell)->addText($verified_by[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
          }
        }else{
          $table->addCell(6000,$styleCell)->addText("",Null,$noSpaceAndCenter);
        }

        $table->addRow(1);
        $table->addCell(6000)->addText('Name : '.$Moderator[0]->name, null, $noSpaceAndLeft);
        $table->addCell(6000)->addText('Name : '.$verified_by[0]->name, null, $noSpaceAndLeft);

        $table->addRow(1);
        $table->addCell(6000)->addText('Date: '.$action->moderator_date,null, $noSpaceAndLeft);
        $table->addCell(6000)->addText('Date: '.$action->verified_date,null, $noSpaceAndLeft);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
        return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
    }


    public function createPreviousAss($id,$question){

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

      if(count($TP_Ass)<=0){
        return redirect()->back()->with('Failed',"The Teaching Plan(Assessment Method) are related with assessment list. So, Please fill in that first.");
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
        $failed = "The course have not yet open in short semester. Please write down the Assessment list for this course.";
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
        $failed = "The course have not yet open in long semester. Please write down the Assessment list for this course.";
      }

      if(count($previous)>0){
            $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $previous[0]->course_id)
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

            if(count($assessments)>0){
                $removeActive = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

                foreach($removeActive as $ra){
                    $rm = Assessments::where('ass_id', '=', $ra->ass_id)->firstOrFail();
                    $rm_list = AssessmentList::where('ass_id','=',$ra->ass_id)->update(['status' => 'Remove']);
                    $rm->status  = "Remove";
                    $rm->save();
                }

                foreach($assessments as $row){
                    $assessment = new Assessments([
                        'course_id'         =>  $id,
                        'assessment'        =>  $question,
                        'assessment_name'   =>  $row->assessment_name,
                        'CLO'               =>  $row->CLO,
                        'coursemark'        =>  $row->coursemark,
                        'coursework'        =>  $row->coursework,
                        'status'            =>  'Active',
                    ]);
                    $assessment->save();
                }
                return redirect()->back()->with('success','Assessment List Inserted Successfully');
            }else{
              return redirect()->back()->with('Failed',"Your last semester of Assessment List is empty.");
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
