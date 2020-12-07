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
use App\AssFinal;
use App\AssessmentFinal;
use App\Imports\syllabusRead;
use ZipArchive;
use File;
use App\ActionFA_V_A;

class C_FinalExamController extends Controller
{
    public function viewFinalExamination($id)
    {
      $user_id     = auth()->user()->user_id;
      $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
      $faculty_id  = $staff_dean->faculty_id;
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

        $moderator_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
        $moderator_person_name = User::where('user_id', '=', $moderator_by->user_id)->firstOrFail();

        $verified_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'HoD')
                 ->where('staffs.department_id','=',$department_id)
                 ->get();

        // $verified_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        // $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        $approved_person_name = User::where('user_id', '=', $user_id)->firstOrFail();

        if(count($course)>0){
            return view('dean.CoursePortFolio.FinalExam.viewFinalExam',compact('course','ass_final','action','moderator_person_name','verified_by','approved_person_name'));
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

        $lecturer_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        if(count($course)>0){
            $path = storage_path('private/syllabus/'.$course[0]->syllabus);
            $array = (new syllabusRead)->toArray($path);
            return response()->json([$array[0],$lecturer_result]);
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

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
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
            return view('dean.CoursePortFolio.FinalExam.createFinalQuestion',compact('course','mark','coursework','ass_final','TP_Ass','tp'));
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
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $final->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
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
            return view('dean.CoursePortFolio.FinalExam.FinalAssessmentListView', compact('course','final','group_list','assessment_final'));
        }else{
            return redirect()->back();
        }
    }

    public function FinalAssessmentImage($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $checkImageFXID = AssessmentFinal::where('ass_fx_document', '=', $image_name)->firstOrFail();
        $fx_id = $checkImageFXID->fx_id;

        $checkCourseId = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
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
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                 ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                 ->where('courses.course_id', '=', $assessment_final->course_id)
                 ->where('faculty.faculty_id','=',$faculty_id)
                 ->get();

        $assessment_list = DB::table('assessment_final')
                    ->join('ass_final','assessment_final.fx_id','=','ass_final.fx_id')
                    ->join('courses', 'courses.course_id', '=', 'ass_final.course_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->select('assessment_final.*','courses.*','semesters.*','ass_final.*')
                    ->where('ass_final.fx_id', '=', $assessment_final->fx_id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->orderBy('assessment_final.ass_fx_id')
                    ->orderBy('assessment_final.ass_fx_name')
                    ->get();

        if(count($course)>0){
            return view('dean.CoursePortFolio.FinalExam.viewWholePaper', compact('assessment_list','assessment_final'));
        }else{
            return redirect()->back();
        }
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
                    $result .= '<div class="col-12 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->fx_id.'" value="'.$row->fx_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/CourseList/FinalExamination/view_list/'.$row->fx_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.' ( '.$row->coursework.'% )</b></p>';
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
            $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

            if(count($ass_final)>0) {
                foreach ($ass_final as $row) {
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->fx_id.'" value="'.$row->fx_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/CourseList/FinalExamination/view_list/'.$row->fx_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->assessment_name.' ( '.$row->coursework.'% )</b></p>';
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
        $fx_id         = $request->get('fx_id');

        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();
        $course_id = $final->course_id;

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
                            $result .= '<div class="col-11 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="/CourseList/images/final_assessment/'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$final->assessment_name.' / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='."/CourseList/final_assessment/view/whole_paper/".$row->ass_fx_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->ass_fx_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-1" id="course_action">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
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
                            $result .= '<div class="col-11 row align-self-center" >';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->ass_fx_id.'_'.$row->ass_fx_type.'" class="group_'.$row_group->ass_fx_type.' group_download">';
                            $result .= '</div>';
                            $result .= '<a href="/CourseList/images/final_assessment/'.$row->ass_fx_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$final->assessment_name.' / '.$row_group->ass_fx_type.' / '.$row->ass_fx_name.' <br> <a href='."/CourseList/final_assessment/view/whole_paper/".$row->ass_fx_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                              $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                              $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->ass_fx_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-1" id="course_action">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->ass_fx_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;';
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
