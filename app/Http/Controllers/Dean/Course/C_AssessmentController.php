<?php

namespace App\Http\Controllers\Dean\Course;

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

class C_AssessmentController extends Controller
{
    public function Assessment($id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
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

        // $verified_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        // $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        $verified_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'HoD')
                 ->where('staffs.department_id','=',$department_id)
                 ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.Assessment.viewAssessment',compact('course','assessments','action','moderator_person_name','verified_by'));
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

        if(count($course)>0){
            return view('dean.CoursePortFolio.Assessment.createQuestion',compact('course','mark','question','assessments','previous_semester','group_assessments','TP_Ass','coursework'));
        }else{
            return redirect()->back();
        }
    }

    public function assessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $checkImageASSID = AssessmentList::where('ass_document', '=', $image_name)->firstOrFail();
        $ass_id = $checkImageASSID->ass_id;

        $checkCourseId = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $checkCourseId->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
                 ->get();

        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function assessment_list_view($ass_id){
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $question = $assessments->assessment;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $assessments->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
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
            return view('dean.CoursePortFolio.Assessment.AssessmentListView', compact('course','assessments','question','group_list','assessment_list'));
        }else{
            return redirect()->back();
        }
    }

    public function view_wholePaper($ass_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $assessments->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
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
            return view('dean.CoursePortFolio.Assessment.viewWholePaper', compact('assessments','assessment_list','question'));
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
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $assessments->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
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
                    $result .= '<div class="col-12 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->ass_id.'" value="'.$row->ass_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/CourseList/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
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
                    $result .= '<div class="col-12 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->ass_id.'" value="'.$row->ass_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/CourseList/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
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
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $assessments->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
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
                            $result .= '<a href="/CourseList/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='."/CourseList/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
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
                            $result .= '<a href="/CourseList/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->assessment_name.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='."/CourseList/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>'.$row->ass_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_li_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
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
}
