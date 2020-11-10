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
use App\Assessments;
use App\AssessmentList;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

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
                    ->join('assessments','courses.course_id','=','assessments.course_id')
                    ->select('subjects.*','courses.*','semesters.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('courses.semester')
                    ->groupBy('courses.course_id')
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
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
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

        $assessment = new Assessments([
            'course_id'         =>  $course_id,
            'assessment'        =>  $assessment,
            'assessment_name'          =>  $ass_name,
            'status'            =>  'Active',
        ]);
        $assessment->save();

        return redirect()->back()->with('success','New Assessment Added Successfully');
    }

    public function AssessmentNameEdit(Request $request){
        $ass_id = $request->get('value');
        $folder = Assessments::find($ass_id);
        return $folder;
    }

    public function updateAssessmentName(Request $request){
        $ass_id   = $request->get('ass_id');
        $assessment = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $assessment->assessment_name  = $request->get('assessment_name');
        $assessment->save();
        return redirect()->back()->with('success','Edit Folder Name Successfully');
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
                    $result .= '<a href="/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
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
                    $result .= '<a href="/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
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
                            $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
                            $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
        return response()->download($fileName);  
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
        return response()->download($fileName);
    }

    // public function viewPreviousAssessment($id,$course_id)
    // {
    //     $user_id       = auth()->user()->user_id;
    //     $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    //     $faculty_id    = $staff_dean->faculty_id;

    //     $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('lecturer', '=', $staff_dean->id)
    //              ->where('course_id', '=', $id)
    //              ->get();

    //     $assessments = DB::table('assessments')
    //                 ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
    //                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                 ->select('assessments.*','courses.*','semesters.*')
    //                 ->where('assessments.course_id', '=', $course_id)
    //                 ->where('assessments.status', '=', 'Active')
    //                 ->groupBy('assessments.assessment')
    //                 ->get();

    //     if(count($course)>0){
    //         return view('dean.Assessment.viewPreviousAssessment',compact('course','assessments'));
    //     }else{
    //         return redirect()->back();
    //     }
    // }

    // public function searchListKey(Request $request)
    // {
    //     $value         = $request->get('value');
    //     $course_id     = $request->get('course_id');

    //     $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('course_id', '=', $course_id)
    //              ->get();

    //     $result = "";
    //     if($value!=""){
    //         $grouping = DB::table('assessments')
    //                     ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
    //                     ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                     ->select('assessments.*','courses.*','semesters.*')
    //                     ->where('courses.subject_id','=',$course[0]->subject_id)
    //                     ->Where(function($query) use ($value) {
    //                       $query->orWhere('assessments.ass_name','LIKE','%'.$value.'%')
    //                         ->orWhere('assessments.ass_word','LIKE','%'.$value.'%')
    //                         ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
    //                     })
    //                     ->where('assessments.course_id','!=',$course_id)
    //                     ->where('assessments.status', '=', 'Active')
    //                     ->groupBy('assessments.assessment')
    //                     ->orderBy('assessments.assessment')
    //                     ->get();

    //         $result .= '<h5 style="margin-top: 5px;padding-left: 15px;border:0px solid black;" class="col-md-12">Searched Result</h5>';

    //         $result .= '<hr style="color:#d9d9d9;margin:0px 15px;position:relative;top:3px;" class="col">';
    //         $p = 1;
    //         if(count($grouping) >0 ){
    //             foreach($grouping as $ga){
    //                 $current_assessments = DB::table('assessments')
    //                         ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
    //                         ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                         ->select('assessments.*','courses.*','semesters.*')
    //                         ->where('courses.subject_id','=',$course[0]->subject_id)
    //                         ->Where(function($query) use ($value) {
    //                           $query->orWhere('assessments.ass_name','LIKE','%'.$value.'%')
    //                             ->orWhere('assessments.ass_word','LIKE','%'.$value.'%')
    //                             ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
    //                         })
    //                         ->where('assessments.course_id','!=',$course_id)
    //                         ->where('assessments.assessment', '=', $ga->assessment)
    //                         ->where('assessments.status', '=', 'Active')
    //                         ->orderBy('assessments.ass_type')
    //                         ->orderByDesc('semesters.semester_name')
    //                         ->orderBy('assessments.ass_name')
    //                         ->orderBy('assessments.ass_id')
    //                         ->get();

    //                 $result .= '<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
    //                 $result .= '<h5 style="padding:15px 0px 15px 15px;margin:0px;" class="col-md-12 plus" id="'.$p.'">'.$ga->assessment.' (<i class="fa fa-plus" aria-hidden="true" id="icon_'.$p.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
    //                 $result .= '<div id="previous_'.$p.'" class="col-12 row align-self-center list" style="display:none;margin-left:0px;padding:0px;">';
    //                 foreach($current_assessments as $row){
    //                     if($row->ass_place==$row->assessment){
    //                         $original_place = $row->semester_name." : ";
    //                         $title = $row->semester_name." : ".$row->assessment.' / '.$row->ass_name;
    //                     }else{
    //                         $original_place = $row->semester_name." : ";
    //                         $place_name = explode(',,,',($row->ass_place));
    //                         $title = $row->semester_name." : ";
    //                         $i=1;
    //                         while(isset($place_name[$i])!=""){
    //                             $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
    //                             $original_place .= $name->ass_name." / ";
    //                             $title .= $name->ass_name." / ";
    //                             $i++;
    //                         }
    //                         $title .= $row->ass_name;
    //                     }
    //                     if($row->ass_type=="folder"){
    //                         $result .= '<div class="col-12 row align-self-center" id="course_list">';
    //                         $result .= '<a href="/assessment/folder/'.$course[0]->course_id.'/previous/'.$row->ass_id.'/list" id="show_image_link" class="col-9 row align-self-center">';
    //                         $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
    //                         $result .= '<div class="col-1" style="position: relative;top: -2px;">';
    //                         $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
    //                         $result .= '</div>';
    //                         $result .= '<div class="col-10" id="course_name">';
    //                         $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->semester_name." : ".$row->ass_name.'</b></p>';
    //                         $result .= '</div></div></a>';
    //                         $result .= '</div>';
    //                     }else{
    //                         $result .= '<div class="col-12 row align-self-center" id="course_list">';
    //                         $result .= '<a href="/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-9 row align-self-center" id="show_image_link" data-title="'.$title.' <br> <a href='."/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
    //                         $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
    //                         $result .= '<div class="col-1" style="position: relative;top: -2px;">';
    //                         $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
    //                         $result .= '</div>';
    //                         $result .= '<div class="col-10" id="course_name">';

    //                         $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$original_place. '<span style="color:black;">' .$row->ass_name. '</span></b></p>';
    //                         $result .= '</div></div></a>';
    //                         $result .= '<div class="col-3" id="course_action_two">';
    //                         $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
    //                         $result .= '</div></div>';
    //                     }
    //                 }
    //                 $result .= '</div></div>';
    //                 $p++;
    //             }
    //         }else{
    //             $result .= '<div class="col-md-12">';
    //             $result .= '<p>Not Found</p>';
    //             $result .= '</div>';
    //         }
    //     }else{
    //         $previous_semester = DB::table('courses')
    //                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                 ->join('assessments','courses.course_id','=','assessments.course_id')
    //                 ->select('subjects.*','courses.*','semesters.*')
    //                 ->where('subjects.subject_id', '=', $course[0]->subject_id)
    //                 ->where('courses.course_id','!=',$course_id)
    //                 ->where('courses.status', '=', 'Active')
    //                 ->orderByDesc('courses.semester')
    //                 ->groupBy('courses.course_id')
    //                 ->get();
    //         foreach($previous_semester as $row){
    //             $result .= '<div class="col-12 row align-self-center" id="course_list">';
    //             $result .= '<a href="/assessment/'.$course_id.'/previous/'.$row->course_id.'/list" id="show_image_link" class="col-9 row align-self-center">';
    //             $result .= '<div class="col-12 row" style="padding:10px;color:#0d2f81;">';
    //             $result .= '<div class="col-1" style="position: relative;top: -2px;">';
    //             $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
    //             $result .= '</div>';
    //             $result .= '<div class="col-10" id="course_name">';
    //             $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->semester_name.'</b></p>';
    //             $result .= '</div>';
    //             $result .= '</div>';
    //             $result .= '</a>';
    //             $result .= '</div>';
    //         }
    //     }
    //     return $result;
    // }

    // public function viewPreviousQuestion($id,$course_id,$question,$list){
    //     $user_id       = auth()->user()->user_id;
    //     $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    //     $faculty_id    = $staff_dean->faculty_id;

    //     $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('lecturer', '=', $staff_dean->id)
    //              ->where('course_id', '=', $id)
    //              ->get();

    //     $assessments = DB::table('assessments')
    //                 ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
    //                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                 ->select('assessments.*','courses.*','semesters.*')
    //                 ->where('assessments.course_id', '=', $course_id)
    //                 ->where('assessments.ass_place', '=', $question)
    //                 ->where('assessments.status', '=', 'Active')
    //                 ->orderBy('assessments.ass_id')
    //                 ->orderBy('assessments.ass_name')
    //                 ->get();

    //     if(count($course)>0){
    //         return view('dean.Assessment.viewPreviousQuestion',compact('course','question','assessments','list'));
    //     }else{
    //         return redirect()->back();
    //     }
    // }

    // public function previous_folder_view($id,$folder_id,$list)
    // {
    //     $user_id       = auth()->user()->user_id;
    //     $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    //     $faculty_id    = $staff_dean->faculty_id;

    //     $course = DB::table('courses')
    //              ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
    //              ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //              ->select('courses.*','subjects.*','semesters.*')
    //              ->where('lecturer', '=', $staff_dean->id)
    //              ->where('course_id', '=', $id)
    //              ->get();

    //     $assessments = DB::table('assessments')
    //                 ->join('courses', 'courses.course_id', '=', 'assessments.course_id')
    //                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
    //                 ->select('assessments.*','courses.*','semesters.*')
    //                 ->where('assessments.ass_id', '=', $folder_id)
    //                 ->get();

    //     $question = $assessments[0]->assessment;
        
    //     $place_name = explode(',,,',($assessments[0]->ass_place));
    //     $i=1;
    //     $data = $question;
    //     while(isset($place_name[$i])!=""){
    //         $name = Assessment::where('ass_id', '=', $place_name[$i])->firstOrFail();
    //         $data .= ",,,".$name->ass_name;
    //         $i++;
    //     }

    //     $ass_place = $assessments[0]->ass_place.",,,".$assessments[0]->ass_id;

    //     $assessment_list = DB::table('assessments')
    //                 ->select('assessments.*')
    //                 ->where('course_id', '=', $assessments[0]->course_id)
    //                 ->where('ass_place', '=', $ass_place)
    //                 ->where('status', '=', 'Active')
    //                 ->orderBy('assessments.ass_name')
    //                 ->get();

    //     if(count($course)>0){
    //         return view('dean.Assessment.AssessmentPreviousFolderView', compact('course','ass_place','assessments','assessment_list','data','question','list'));
    //     }else{
    //         return redirect()->back();
    //     }
    // }
}
