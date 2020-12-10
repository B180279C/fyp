<?php

namespace App\Http\Controllers\Dean\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\Assessments;
use App\AssessmentResultStudent;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class M_AssessmentResultController extends Controller
{
    public function viewAssessmentStudentResult($id,$question)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $id)
                 ->get();

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        if((count($course)>0)&&(count($assessments)>0)){
            return view('dean.Moderator.AssessmentResult.viewAssessmentStudentResult',compact('course','question','assessments'));
        }else{
            return redirect()->back();
        }
    } 

    public function viewstudentlist($ass_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $assessments->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $course_id)
                 ->get();

        $lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        $student_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Students')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        if(count($course)>0){
            return view('dean.Moderator.AssessmentResult.viewStudentResult',compact('course','assessments','lecturer_result','student_result'));
        }else{
            return redirect()->back();
        }
    }

    public function viewStudentResult($ar_stu_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment_result_student = AssessmentResultStudent::where('ar_stu_id', '=', $ar_stu_id)->firstOrFail();
        $ass_id = $assessment_result_student->ass_id;
        $student_id = $assessment_result_student->student_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $assessments->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $course_id)
                 ->get();

        $lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->where('assessment_result_students.student_id','=',$student_id)
                 ->orderBy('assessment_result_students.document_name')
                 ->get();

        $student_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Students')
                 ->where('assessment_result_students.status','=','Active')
                 ->where('assessment_result_students.student_id','=',$student_id)
                 ->orderBy('assessment_result_students.document_name')
                 ->get();

        if(count($course)>0){
            return view('dean.Moderator.AssessmentResult.viewResultList',compact('course','assessments','assessment_result_student','lecturer_result','student_result'));
        }else{
            return redirect()->back();
        }
    }

    public function downloadDocument($ar_stu_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $assessment_result_student = AssessmentResultStudent::where('ar_stu_id', '=', $ar_stu_id)->firstOrFail();
        $ass_id = $assessment_result_student->ass_id;
        $student_id = $assessment_result_student->student_id;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $assessments->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $course_id)
                 ->get();

        if(count($course)>0){
            $ext = "";
            if($assessment_result_student->document!=""){
                $ext = explode(".", $assessment_result_student->document);
            }
            return Storage::disk('private')->download('Assessment_Result/'.$assessment_result_student->document, $assessment_result_student->document_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function assessmentResult_image($image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $checkRSID = AssessmentResultStudent::where('document', '=', $image_name)->firstOrFail();
        $ass_id = $checkRSID->ass_id;

        $checkImageCID = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();
        $course_id = $checkImageCID->course_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $course_id)
                 ->get();

        if(count($course)>0){
            $storagePath = storage_path('/private/Assessment_Result/' . $image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function searchAssessmentForm(Request $request)
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
            $assessment_results = DB::table('assessments')
                 ->select('assessments.*')
                 ->Where(function($query) use ($value) {
                    $query->orWhere('assessments.assessment_name','LIKE','%'.$value.'%');
                 })
                 ->where('course_id', '=', $course_id)
                 ->where('assessment','=',$question)
                 ->where('status','=','Active')
                 ->orderBy('assessments.assessment_name')
                 ->get();
            if(count($assessment_results)>0) {
                foreach($assessment_results as $row){
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->ass_id.'" value="'.$row->ass_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/Moderator/AssessmentResult/studentResult/'.$row->ass_id.'/" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
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
                $result .= '<div class="col-md-12" style="border:0px solid black;padding-top:10px;padding-left:25px;">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $assessment_results = DB::table('assessments')
                     ->select('assessments.*')
                     ->where('course_id', '=', $course_id)
                     ->where('status','=','Active')
                     ->where('assessment','=',$question)
                     ->orderBy('assessments.assessment_name')
                     ->get();
            if(count($assessment_results)>0) {
                foreach($assessment_results as $row){
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center" style="padding-left: 20px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" name="group'.$row->ass_id.'" value="'.$row->ass_id.'" class="group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/Moderator/AssessmentResult/studentResult/'.$row->ass_id.'/" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
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
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin:5px 20px;">
                                <center>Empty</center></div>';
            }
        }
        return $result;
    }

    public function view_wholePaper($ar_stu_id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $checkARID = AssessmentResultStudent::where('ar_stu_id', '=', $ar_stu_id)->firstOrFail();
        $ass_id = $checkARID->ass_id;
        $submitted_by = $checkARID->submitted_by;

        $assessments = Assessments::where('ass_id', '=', $ass_id)->firstOrFail();

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $assessments->course_id)
                 ->get();

        $assessment_result_list = DB::table('assessment_result_students')
                                ->select('assessment_result_students.*')
                                ->where('assessment_result_students.ass_id','=',$ass_id)
                                ->where('assessment_result_students.submitted_by','=',$checkARID->submitted_by)
                                ->where('assessment_result_students.student_id','=',$checkARID->student_id)
                                ->get();
        if(count($course)>0){
            return view('dean.Moderator.AssessmentResult.viewWholePaper', compact('assessment_result_list','assessments','checkARID','submitted_by'));
        }else{
            return redirect()->back();
        }
    }

    public function searchStudentList(Request $request)
    {
        $value         = $request->get('value');
        $course_id     = $request->get('course_id');
        $ass_id     = $request->get('ass_id');

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }

        $result = "";
        if($value!=""){
            $result_list = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->Where(function($query) use ($value) {
                    $query->orWhere('assessment_result_students.student_id','LIKE','%'.$value.'%')
                        ->orWhere('students.batch','LIKE','%'.$value.'%')
                        ->orWhere('users.name','LIKE','%'.$value.'%');
                 })
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

            $result .= '<div class="col-12 row" style="padding: 0px 10px;margin: 0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" id="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<div class="l_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">';
            $result .= '<div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">';
            $result .= '    Searched Result (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 7px;"></i>)';
            $result .= '</div>';
            $result .= '<div class="col-9 show_count" style="border:0px solid black;">';
            $result .= '    <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">';
            $result .= '    <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( '.count($result_list).' ) </span>';
            $result .= '</div></div></div>';
            if(count($result_list)>0) {
            $result .= '<div class="row col-md-12" id="lecturer"  style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">';
                foreach($result_list as $row){
                    $result .= '<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->student_id.'_All" class="group_lecturer group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/Moderator/AssessmentResult/view/student/'.$row->ar_stu_id.'/" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
                    $result .= '<div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">';
                    $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>'.$row->student_id.' ( '.$row->name.' ) </b></p>';
                    $result .= '</div></div></a></div>';
                }
            }else{
                $result .= '<div class="col-md-12" id="lecturer" style="border:0px solid black;padding-top:10px;padding-left:25px;">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

            $student_result = DB::table('assessment_result_students')
                     ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                     ->join('users','users.user_id', '=', 'students.user_id')
                     ->select('assessment_result_students.*','students.*','users.*')
                     ->where('assessment_result_students.ass_id', '=', $ass_id)
                     ->where('assessment_result_students.submitted_by','=', 'Students')
                     ->where('assessment_result_students.status','=','Active')
                     ->groupBy('assessment_result_students.student_id')
                     ->get();

            if(count($lecturer_result)>0) {
            $result .= '<div class="col-12 row" style="padding: 0px 10px;margin: 0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" id="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<div class="l_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">';
            $result .= '<div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">';
            $result .= 'Submitted By Lecturer (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 7px;"></i>)';
            $result .= '</div>';
            $result .= '<div class="col-9 show_count" style="border:0px solid black;">';
            $result .= '    <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">';
            $result .= '    <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( '.count($lecturer_result).' ) </span>';
            $result .= '</div></div></div>';
            
            $result .= '<div class="row col-md-12" id="lecturer"  style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">';
                foreach($lecturer_result as $row){
                    $result .= '<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->student_id.'_Lecturer" class="group_lecturer group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/Moderator/AssessmentResult/view/student/'.$row->ar_stu_id.'/" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
                    $result .= '<div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">';
                    $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>'.$row->student_id.' ( '.$row->name.' ) </b></p>';
                    $result .= '</div></div></a></div>';
                }
            }
            $result .= '</div>';
            
            if(count($student_result)>0) {
                $result .= '<div class="col-12 row" style="padding: 0px 10px;margin: 15px 0px 0px 0px;">';
                $result .= '<div class="checkbox_group_style align-self-center">';
                $result .= '<input type="checkbox" name="group_student" id="group_student" class="group_checkbox">';
                $result .= '</div>';
                $result .= '<div class="s_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">';
                $result .= '<div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">';
                $result .= 'Submitted By Students (<i class="fa fa-minus" aria-hidden="true" id="icon_s" style="color: #0d2f81;position: relative;top: 7px;"></i>)';
                $result .= '</div>';
                $result .= '<div class="col-9 show_count" style="border:0px solid black;">';
                $result .= '    <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">';
                $result .= '    <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( '.count($student_result).' ) </span>';
                $result .= '</div></div></div>';

                $result .= '<div class="row col-md-12" id="student"  style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">';
            
                foreach($student_result as $sow){
                    $result .= '<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$sow->student_id.'_Students" class="group_student group_download">';
                    $result .= '</div>';
                    $result .= '<a href="'.$character.'/Moderator/AssessmentResult/view/student/'.$sow->ar_stu_id.'/" class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">';
                    $result .= '<div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">';
                    $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>'.$sow->student_id.' ( '.$sow->name.' ) </b></p>';
                    $result .= '</div></div></a></div>';
                }
            }

            if((count($lecturer_result)==0)&&(count($student_result)==0)){
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin:5px 20px;">
                                <center>Empty</center></div>';
            }
        }
        return $result;
    }
}
