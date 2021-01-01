<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Imports\syllabusRead;
use Image;
use App\User;
use App\Staff;
use App\Department;
use App\Programme;
use App\Faculty;
use App\Subject;
use ZipArchive;
use File;

class ReportAssessmentController extends Controller
{
  public function viewCADetail($id)
  {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

        if(auth()->user()->position=="Dean"){
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
        }else if(auth()->user()->position=="HoD"){
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
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();
        

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $action = DB::table('actionCA_v_a')
                  ->select('actionCA_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('actionCA_id')
                  ->get();

        $action_big = DB::table('actionCA_v_a')
                  ->select('actionCA_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderByDesc('actionCA_id')
                  ->get();

        $all_assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->orderBy('assessments.ass_id')
                    ->get();

        $moderator_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
        $moderator_person_name = User::where('user_id', '=', $moderator_by->user_id)->firstOrFail();

        $verified_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        if(count($course)>0){
            return view('dean.Report.viewAssessment',compact('course','assessments','all_assessments','TP_Ass','action','moderator_person_name','verified_person_name','action_big'));
        }else{
            return redirect()->back();
        }
  }
  public function getCAaction($course_id)
  {
    $course_CA_action = DB::table('actionca_v_a')
                  ->join('courses','courses.course_id','=','actionca_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('actionca_v_a.*','courses.*','subjects.*','staffs.*','users.*','actionca_v_a.status as action_status')
                  ->where('courses.course_id','=',$course_id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('actionca_v_a.actionCA_id')
                  ->first();

    $status = "Pending";
    if($course_CA_action!=""){
      $status = $course_CA_action->action_status;
    }
    return $status;
  }

  public function searchCourse(Request $request)
  {
    $user_id     = auth()->user()->user_id;
    $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
    $faculty_id  = $staff_dean->faculty_id;
    $department_id = $staff_dean->department_id;
    $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
    $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
    $semester_id = $last_semester->semester_id;

    $value = $request->get('value');
    $result = "";
        if($value!=""){
            if(auth()->user()->position=="Dean"){
                $character = "";
                $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                    })
                    ->orderByDesc('semester_name')
                    ->orderByDesc('course_id')             
                    ->get();
            }else if(auth()->user()->position=="HoD"){
                 $character = "/hod";
                 $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.department_id', '=', $department_id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                    })
                    ->orderByDesc('semester_name')
                    ->orderByDesc('course_id')             
                    ->get();     
            }
            $result .= '<div class="col-12 row" style="padding: 0px 20px 5px 20px;margin:0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" id="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">';
            $result .= 'Search Filter : '.$value;
            $result .= '</p>';
            $result .= '</div>';
            if($course->count()) {
                foreach($course as $row){
                    $status = $this->getCAaction($row->course_id);
                    $color = "grey";
                    if($status == "Rejected"){
                      $color = "red";
                    }else if($status == "Verified"){
                      $color = "green";
                    }else if($status == "Pending"){
                      $color = "grey";
                    }else{
                      $color = "blue";
                    }
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-10 row align-self-center" style="border:0px solid black;margin: 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    if($status!="Pending"&&$status!="Waiting For Moderation"){
                      $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
                    }else{
                      $result .= '<input type="hidden" value="'.$row->course_id.'">';
                    }
                    $result .= '</div>';
                    if($status!="Pending"&&$status!="Waiting For Moderation"){
                      $result .= '<a href="'.$character.'/report/CA/view/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">';
                    }else{
                      $result .= '<a id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;" onclick="showMessage()">';
                    }
                    $result .= '<div class="col-1 align-self-center" id="course_image">';
                    $result .= '<img src="'.url('image/assessment.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-11 align-self-center" id="course_name">';
                    $result .= '<p id="file_name"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.' )</p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-2 align-self-center" id="course_action">';
                    $result .= '<p style="padding:0px;margin:0px;color:'.$color.'">'.$status.'</span>';
                    $result .= '</div>';
                    $result .= '</div>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p><center>No matching records found</center></p>';
                    $result .= '</div>';
            }
        }else{
            if(auth()->user()->position=="Dean"){
                $character = "";
                $course_reviewer = DB::table('courses')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                        ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                        ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                        ->join('staffs', 'staffs.id','=','courses.lecturer')
                        ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                        ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                        ->where('courses.semester','=',$semester_id)
                        ->where('departments.faculty_id','=',$faculty_id)
                        ->where('courses.status','=','Active')
                        ->orderByDesc('course_id')
                        ->get();
            }else if(auth()->user()->position=="HoD"){
                 $character = "/hod";
                 $course_reviewer = DB::table('courses')
                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                        ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                        ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                        ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                        ->join('staffs', 'staffs.id','=','courses.lecturer')
                        ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                        ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                        ->where('courses.semester','=',$semester_id)
                        ->where('departments.department_id','=',$department_id)
                        ->where('courses.status','=','Active')
                        ->orderByDesc('course_id')
                        ->get();        
            }
            $result .= '<div class="col-12 row" style="padding: 0px 20px 5px 20px;margin:0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" id="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">';
            $result .= 'Newest Semester of Courses';
            $result .= '</p>';
            $result .= '</div>';
            foreach($course_reviewer as $row){
                $status = $this->getCAaction($row->course_id);
                $color = "grey";
                if($status == "Rejected"){
                    $color = "red";
                }else if($status == "Verified"){
                    $color = "green";
                }else if($status == "Pending"){
                    $color = "grey";
                }else{
                    $color = "blue";
                }
                $result .= '<div class="col-12 row align-self-center" id="course_list">';
                $result .= '<div class="col-10 row align-self-center" style="border:0px solid black;margin: 0px;">';
                $result .= '<div class="checkbox_style align-self-center">';
                if($status!="Pending"&&$status!="Waiting For Moderation"){
                  $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
                }else{
                  $result .= '<input type="hidden" value="'.$row->course_id.'">';
                }
                $result .= '</div>';

                if($status!="Pending"&&$status!="Waiting For Moderation"){
                  $result .= '<a href="'.$character.'/report/CA/view/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">';
                }else{
                  $result .= '<a id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;" onclick="showMessage()">';
                }
                $result .= '<div class="col-1 align-self-center" id="course_image">';
                $result .= '<img src="'.url('image/assessment.png').'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col-11 align-self-center" id="course_name">';
                $result .= '<p id="file_name"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.' )</p>';
                $result .= '</div>';
                $result .= '</a>';
                $result .= '</div>';
                $result .= '<div class="col-2 align-self-center" id="course_action">';
                $result .= '<p style="padding:0px;margin:0px;color:'.$color.'">'.$status.'</span>';
                $result .= '</div>';
                $result .= '</div>';
            }
        }
    return $result;
  }
	public function DownloadAssessmentReport($id)
	{
		$user_id       = auth()->user()->user_id;
	    $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
	    $faculty_id    = $staff_dean->faculty_id;
	    $department_id = $staff_dean->department_id;

	    if(auth()->user()->position=="Dean"){
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
        }else if(auth()->user()->position=="HoD"){
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
                     ->where('departments.department_id','=',$department_id)
                     ->get();
        }

      if(count($course)==0){
        return redirect()->back();
      }

	   	$action = DB::table('actionca_v_a')
                  ->select('actionca_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderByDesc('actionCA_id')
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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action[0]->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $all_assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action[0]->course_id)
                    ->orderBy('assessments.ass_id')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $action[0]->course_id)
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
                $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
                $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $full_suggest = explode('///NextAss///',$action[0]->suggest);
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
        if($action[0]->moderator_date!=NULL){
          if($Moderator[0]->staff_sign!=NULL){
            $s_p = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
            $table->addCell(6000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
          }else{
            $table->addCell(6000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
          }
        }else{
          $table->addCell(6000,$styleCell)->addText("",Null,$noSpaceAndCenter);
        }

        if($action[0]->verified_date!=NULL){
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
        $table->addCell(6000)->addText('Date: '.$action[0]->moderator_date,null, $noSpaceAndLeft);
        $table->addCell(6000)->addText('Date: '.$action[0]->verified_date,null, $noSpaceAndLeft);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
        return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
	}


	public function ZipFilesDownloadAssessmentReport($course_id,$download)
	{
		if($download=="checked"){
        	$string = explode('---',$course_id);
    }
    $user_id       = auth()->user()->user_id;
    $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    $faculty_id    = $staff_dean->faculty_id;
    $department_id = $staff_dean->department_id;

    for($p=0;$p<(count($string)-1);$p++){
      if(auth()->user()->position=="Dean"){
          $course = DB::table('courses')
                       ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                       ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                       ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                       ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                       ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                       ->join('staffs', 'staffs.id','=','courses.lecturer')
                       ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                       ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                       ->where('courses.course_id', '=', $string[$p])
                       ->where('faculty.faculty_id','=',$faculty_id)
                       ->get();
      }else if(auth()->user()->position=="HoD"){
          $course = DB::table('courses')
                       ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                       ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                       ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                       ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                       ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                       ->join('staffs', 'staffs.id','=','courses.lecturer')
                       ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                       ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                       ->where('courses.course_id', '=', $string[$p])
                       ->where('departments.department_id','=',$department_id)
                       ->get();
      }
      if(count($course)==0){
        return redirect()->back();
      }
    }

	  $name = "Moderation Form (CA) Zip Files";
    $zip = new ZipArchive;
    $fileName = storage_path('private/Assessment/Zip_Files/'.$name.'.zip');
    $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    for($c=0;$c<(count($string)-1);$c++){
	    $course = DB::table('courses')
	                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
	                 ->join('programmes','subjects.programme_id','=','programmes.programme_id')
	                 ->join('departments','programmes.department_id','=','departments.department_id')
	                 ->join('faculty','departments.faculty_id','=','faculty.faculty_id')
	                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
	                 ->join('staffs','staffs.id','=','courses.lecturer')
	                 ->join('users','staffs.user_id','=','users.user_id')
	                 ->select('courses.*','subjects.*','semesters.*','programmes.*','staffs.*','users.*','faculty.*')
	                 ->where('course_id', '=', $string[$c])
	                 ->get();

	   	$action = DB::table('actionca_v_a')
                  ->select('actionca_v_a.*')
                  ->where('course_id', '=', $string[$c])
                  ->orderByDesc('actionCA_id')
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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action[0]->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $all_assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action[0]->course_id)
                    ->orderBy('assessments.ass_id')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $action[0]->course_id)
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
                $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
                $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $AccOrRec_list = explode('///',$action[0]->AccOrRec);
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
            $full_suggest = explode('///NextAss///',$action[0]->suggest);
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
        if($action[0]->moderator_date!=NULL){
          if($Moderator[0]->staff_sign!=NULL){
            $s_p = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
            $table->addCell(6000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
          }else{
            $table->addCell(6000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
          }
        }else{
          $table->addCell(6000,$styleCell)->addText("",Null,$noSpaceAndCenter);
        }

        if($action[0]->verified_date!=NULL){
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
        $table->addCell(6000)->addText('Date: '.$action[0]->moderator_date,null, $noSpaceAndLeft);
        $table->addCell(6000)->addText('Date: '.$action[0]->verified_date,null, $noSpaceAndLeft);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($course[0]->semester_name." ".$course[0]->subject_code." ".$course[0]->subject_name.'_CA.docx');
		$zip->addFile($course[0]->semester_name." ".$course[0]->subject_code." ".$course[0]->subject_name.'_CA.docx',$course[0]->semester_name." ".$course[0]->subject_code." ".$course[0]->subject_name.'_CA.docx');
		}
		$zip->close();
		for($i=0;$i<(count($string)-1);$i++){
          	$user_id       = auth()->user()->user_id;
            $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id    = $staff_dean->faculty_id;
            $department_id = $staff_dean->department_id;

            $course = DB::table('courses')
                      ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                      ->join('programmes','subjects.programme_id','=','programmes.programme_id')
                      ->join('departments','programmes.department_id','=','departments.department_id')
                      ->join('faculty','departments.faculty_id','=','faculty.faculty_id')
                      ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                      ->join('staffs','staffs.id','=','courses.lecturer')
                      ->join('users','staffs.user_id','=','users.user_id')
                      ->select('courses.*','subjects.*','semesters.*','programmes.*','staffs.*','users.*','faculty.*')
                      ->where('course_id', '=', $string[$i])
                      ->get();

            foreach($course as $row){
                $syllabus = $row->syllabus;
                $faculty_name = $row->faculty_name;
                $programme_name = $row->programme_name;
                $semester_name = $row->semester_name;
                $subject_code = $row->subject_code;
                $subject_name = $row->subject_name;
                $lecture_name = $row->name;
                $year = $row->year;
                $semester = $row->semester;
                File::delete($semester_name." ".$subject_code." ".$subject_name.'_CA.docx');
            }
      	}
      return response()->download($fileName)->deleteFileAfterSend(true);
    }
}