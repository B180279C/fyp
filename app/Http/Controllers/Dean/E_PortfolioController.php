<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\User;
use App\Staff;
use App\Department;
use App\Programme;
use App\Faculty;
use App\Subject;
use ZipArchive;
use File;

class E_PortfolioController extends Controller
{
	public function viewE_Portfolio($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $syllabus = $course[0]->syllabus;

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $assessment_list = DB::table('assessment_list')
        			->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->select('assessment_list.*','assessments.*')
                    ->where('assessment_list.status', '=', 'Active')
                    ->where('assessments.course_id', '=', $id)
                    ->get();

        $lecturer_result = DB::table('assessment_result_students')
        		 ->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
                 ->select('assessment_result_students.*','assessments.*')
                 ->where('assessments.course_id', '=', $id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->groupBy('assessments.ass_id')
                 ->get();

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

        $assessment_final = DB::table('assessment_final')
        			->join('ass_final','ass_final.fx_id','=','assessment_final.fx_id')
                    ->select('assessment_final.*','ass_final.*')
                    ->where('ass_final.course_id', '=', $id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->get();

        $lecturer_fx_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        $action = DB::table('action_v_a')
                    ->select('action_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $ca_action = DB::table('actionca_v_a')
                    ->select('actionca_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Verified')
                    ->get();

        $fa_action = DB::table('actionfa_v_a')
                    ->select('actionfa_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->get();

        return view('dean.E_portfolio.viewE_Portfolio',compact('course','assessments','assessment_list','lecturer_result','ass_final','assessment_final','lecturer_fx_result','syllabus','action','ca_action','fa_action','lecture_note'));
	}

	public function Download_E_Portfolio($id)
	{
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
	              ->where('course_id', '=', $id)
	              ->where('faculty.faculty_id','=',$faculty_id)
	              ->get();

	    $syllabus = $course[0]->syllabus;

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $assessment_list = DB::table('assessment_list')
        			->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->select('assessment_list.*','assessments.*')
                    ->where('assessment_list.status', '=', 'Active')
                    ->where('assessments.course_id', '=', $id)
                    ->get();

        $lecturer_result = DB::table('assessment_result_students')
        		 ->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
                 ->select('assessment_result_students.*','assessments.*')
                 ->where('assessments.course_id', '=', $id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->groupBy('assessments.ass_id')
                 ->get();

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

        $assessment_final = DB::table('assessment_final')
        			->join('ass_final','ass_final.fx_id','=','assessment_final.fx_id')
                    ->select('assessment_final.*','ass_final.*')
                    ->where('ass_final.course_id', '=', $id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->get();

        $lecturer_fx_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        $action = DB::table('action_v_a')
                    ->select('action_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $ca_action = DB::table('actionca_v_a')
                    ->select('actionca_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Verified')
                    ->get();

        $fa_action = DB::table('actionfa_v_a')
                    ->select('actionfa_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->get();

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
	    $table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('E-PORTFOLIO FORM'),null,$noSpaceAndCenter);
	    $table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
	    $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

	    $table->addRow(1);
	    $table->addCell(null, $cellRowContinue);
	    $table->addCell(null, $cellRowContinue);
	    $table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
	    $table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

	    $textrun = $header->addTextRun();
	    $textrun->addText("",null,$noSpaceAndCenter);

	    $CA_full_title = $section->addText('E-PORTFOLIO FORM',array('bold' => true),$noSpaceAndCenter);

	    $textrun = $section->addTextRun();
	    $textrun->addText("",null,$noSpaceAndCenter);

	    $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
	    $phpWord->addTableStyle('title', $styleTable);
	    $title = $section->addTable('title');
	    // $section->addTextBreak(1);
	    $title->addRow();
	    $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Course Information',array('bold' => true),$noSpaceAndCenter);

	    $textrun = $section->addTextRun();
	    $textrun->addText("",null,$noSpaceAndCenter);

	    $styleTable = array('cellMargin' => 60);
	    $fontStyle = array('bold' => true);
	    $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
	    $phpWord->addTableStyle('Course Table', $styleTable);
	    $course_table = $section->addTable('Course Table');
	    $styleCell = array('valign' => 'center','borderSize' => 6);
	    $cellColSpan = array('gridSpan' => '2','valign' => 'bottom');
	    $course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Faculty', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText($course[0]->faculty_name, null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Programme', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText($course[0]->programme_name, null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Subject Code', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText($course[0]->subject_code, null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Subject Name', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText($course[0]->subject_name, null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Course Coordinator', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText($course[0]->name, null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Lecturer Name', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText($course[0]->name, null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Academic Year / Semester', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText('20'.$course[0]->year."_".$course[0]->semester, null, $noSpaceAndLeft);

    	$course_table->addRow(500);
    	$course_table->addCell(12000,$cellColSpan)->addText('(Lecturer to decide their own creativity to fill up this section)', null, $noSpaceAndLeft);

    	$course_table->addRow(1);
	    $course_table->addCell(3000,$styleCell)->addText('Introduction to Reader', null, $noSpaceAndLeft);
    	$course_table->addCell(9000,$styleCell)->addText("- To brief summary of specific teaching experiences and qualifications to date.<w:br/>- Intro lecturer's portfolio, organization, items and etc.<w:br/>- Sign and date", null, $noSpaceAndLeft);

    	$textrun = $section->addTextRun();
    	$textrun->addText("",null,$noSpaceAndCenter);

    	$styleTable = array('borderSize' => 6, 'borderColor' => 'black');
	    $phpWord->addTableStyle('title', $styleTable);
	    $title = $section->addTable('title');
	    // $section->addTextBreak(1);
	    $title->addRow();
	    $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('E - Portfolio',array('bold' => true),$noSpaceAndCenter);

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
	    $cellColSpan = array('gridSpan' => 3,'valign' => 'center','bgColor' => 'cccccc');
	    $cellRowNorSpan = array('vMerge' => 'restart','valign' => 'top');
	    $cellRowNorContinue = array('vMerge' => 'continue','valign' => 'center');
	    $table->addRow(1);
	    $table->addCell(2000,$cellRowSpan)->addText('Folder Name',$fontStyle, $noSpaceAndCenter);
    	$table->addCell(4000,$cellRowSpan)->addText("Documents", $fontStyle, $noSpaceAndCenter);
    	$table->addCell(6000,$cellColSpan)->addText('Softcopy',$fontStyle, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowContinue);
    	$table->addCell(4000,$cellRowContinue);
    	$table->addCell(2000,$styleThCell)->addText('Course Coordinator',$fontStyle, $noSpaceAndCenter);
    	$table->addCell(2000,$styleThCell)->addText('Moderator',$fontStyle, $noSpaceAndCenter);
    	$table->addCell(2000,$styleThCell)->addText('Audit Committee',$fontStyle, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorSpan)->addText('1. Course Information',$fontStyle,$noSpaceAndLeft);
    	$table->addCell(4000,$styleCell)->addText('a) Syllabus',Null,$noSpaceAndLeft);
    	if($syllabus!=""){
    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
    	}else{
    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	}
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('b) Teaching Plan',Null,$noSpaceAndLeft);
    	if(count($action)>0){
    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
    	}else{
    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	}
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('c) Internal Moderation of Continuous Assessment',Null,$noSpaceAndLeft);
    	if(count($ca_action)>0){
    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
    	}else{
    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	}
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('d) Internal Moderation of Final Examination Paper',Null,$noSpaceAndLeft);
    	if(count($fa_action)>0){
    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
    	}else{
    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	}
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('e) External Moderation of Final Examination Paper ( if application )',Null,$noSpaceAndLeft);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('f) CA Marks with Excel Format (by programme)',Null,$noSpaceAndLeft);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('g) Final Marks with Excel Format (by programme)',Null,$noSpaceAndLeft);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('h) Timetable',Null,$noSpaceAndLeft);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('i) Attendance',Null,$noSpaceAndLeft);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorSpan)->addText('2. Teaching Material',$fontStyle,$noSpaceAndLeft);
    	$table->addCell(4000,$styleCell)->addText('Lecture slides, documents and other teaching materials',Null,$noSpaceAndLeft);
    	if(count($lecture_note)>0){
			$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
    	}else{
    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	}
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$num = 3;
    	foreach($assessments as $row){

    		$table->addRow(1);
	    	$table->addCell(2000,$cellRowNorSpan)->addText($num.'. '.$row->assessment_name,$fontStyle,$noSpaceAndLeft);
	    	$table->addCell(4000,$styleCell)->addText('a) Moderated Question(s)',Null,$noSpaceAndLeft);
	    	$question = 0;
	    	foreach($assessment_list as $row_list){
				if($row->ass_id == $row_list->ass_id){
					if($row_list->ass_type=="Question"){
						$question++;
					}
				}
	    	}
	    	if($question>0){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

	    	$table->addRow(1);
	    	$table->addCell(3000,$cellRowNorContinue);
	    	$table->addCell(3000,$styleCell)->addText('b) Moderated Marking Scheme(s) / Solution(s)',Null,$noSpaceAndLeft);
	    	$solution = 0;
	    	foreach($assessment_list as $row_list){
				if($row->ass_id == $row_list->ass_id){
					if($row_list->ass_type=="Solution"){
						$solution++;
					}
				}
	    	}
	    	if($solution>0){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

	    	$table->addRow(1);
	    	$table->addCell(3000,$cellRowNorContinue);
	    	$table->addCell(3000,$styleCell)->addText('c) Samples ( 9 samples : 3 Good; 3 Average; 3 poor )',Null,$noSpaceAndLeft);
	    	$result = 0;
			foreach($lecturer_result as $row_result){
				if($row->ass_id == $row_result->ass_id){
					$result++;
				}
			}
			if($result>=9){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$num++;
    	}	

    	if(count($ass_final)>0){
    		$table->addRow(1);
	    	$table->addCell(2000,$cellRowNorSpan)->addText($num.'. '.$row->assessment_name,$fontStyle,$noSpaceAndLeft);
	    	$table->addCell(4000,$styleCell)->addText('a) Moderated Examination Paper',Null,$noSpaceAndLeft);
	    	$f_q = 0;
	    	if(count($assessment_final)>0){
				foreach($assessment_final as $row_final){
					if($row_final->ass_fx_type=="Question"){
						$f_q++;
					}
				}
	        }
	        if($f_q>0){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

	    	$table->addRow(1);
	    	$table->addCell(3000,$cellRowNorContinue);
	    	$table->addCell(3000,$styleCell)->addText('b) Moderated Examination Paper Marking Scheme',Null,$noSpaceAndLeft);
	    	$f_s = 0;
	    	if(count($assessment_final)>0){
				foreach($assessment_final as $row_final){
					if($row_final->ass_fx_type=="Solution"){
						$f_s++;
					}
				}
	        }
	        if($f_s>0){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

	    	$table->addRow(1);
	    	$table->addCell(3000,$cellRowNorContinue);
	    	$table->addCell(3000,$styleCell)->addText('c) Final Exam Scripts Moderation for Course File Form',Null,$noSpaceAndLeft);
	    	if(count($fa_action)>0){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

	    	$table->addRow(1);
	    	$table->addCell(3000,$cellRowNorContinue);
	    	$table->addCell(3000,$styleCell)->addText('d) Samples ( 9 samples : 3 Good; 3 Average; 3 poor )',Null,$noSpaceAndLeft);
	    	if(count($lecturer_fx_result)>=9){
	    		$table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
	    	}else{
	    		$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	}
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    	$num++;
    	}

    	$table->addRow(1);
	    $table->addCell(2000,$cellRowNorSpan)->addText($num.'. Course Review Report',$fontStyle,$noSpaceAndLeft);
	    $table->addCell(4000,$styleCell)->addText('a) Completed course review report',Null,$noSpaceAndLeft);
	    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
	    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

	    $textrun = $section->addTextRun();
    	$textrun->addText("",null,$noSpaceAndCenter);

    	$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
	    $fontStyle = array('bold' => true);
	    $phpWord->addTableStyle('Sign Table', $styleTable);
	    $table = $section->addTable('Sign Table');
	    $styleCell = array('valign' => 'center');
	    $table->addRow(1000);
	    $table->addCell(4000)->addText('Lecturer: ',null, $noSpaceAndLeft);
	    $table->addCell(4000)->addText('Verified By Head Of Department: ', null, $noSpaceAndLeft);
	    $table->addCell(4000)->addText('Approved By Dean: ', null, $noSpaceAndLeft);

	    $hod = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'HoD')
                 ->where('staffs.department_id','=',$department_id)
                 ->get();

        $dean = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('users.position', '=', 'Dean')
                 ->where('staffs.faculty_id','=',$faculty_id)
                 ->get();

	    $table->addRow(1);
	    $table->addCell(4000)->addText('Name: '.$course[0]->name,null, $noSpaceAndLeft);
	    $table->addCell(4000)->addText('Name: '.$hod[0]->name, null, $noSpaceAndLeft);
	    $table->addCell(4000)->addText('Name: '.$dean[0]->name, null, $noSpaceAndLeft);

	    $table->addRow(1);
	    $table->addCell(4000)->addText('Date: '.date("Y-j-n"),null, $noSpaceAndLeft);
	    $table->addCell(4000)->addText('Date: ',null, $noSpaceAndLeft);
	    $table->addCell(4000)->addText('Date: ',null, $noSpaceAndLeft);

	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
		return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);

        // $name = "E - Portfolio Zip Files";
        // $zip = new ZipArchive;
        // $fileName = storage_path('private/E-Portfolio/Zip_Files/'.$name.'.zip');
        // $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        // $zip->addFile($course[0]->subject_code." ".$course[0]->subject_name.'.docx',$course[0]->subject_code." ".$course[0]->subject_name.'.docx');
        // $zip->close();
        // return response()->download($fileName)->deleteFileAfterSend(true);
	}

	public function E_Portfolio_List()
	{
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;

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
                    ->get();

		return view('dean.E_portfolio.E_Portfolio_List',compact('course_reviewer'));
	}

    public function searchCourse(Request $request)
    {
        $user_id     = auth()->user()->user_id;
        $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id  = $staff_dean->faculty_id;
        $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id = $last_semester->semester_id;

        $value = $request->get('value');

        $result = "";
        if($value!=""){
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
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if($course->count()) {
                foreach($course as $row){
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-11 row align-self-center" style="border:0px solid black;margin: 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
                    $result .= '</div>';
                    $result .= '<a href="/E_Portfolio/list/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">';
                    $result .= '<div class="col-1 align-self-center" id="course_image">';
                    $result .= '<img src="'.url('image/portfolio.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-11 align-self-center" id="course_name">';
                    $result .= '<p id="file_name"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '<div class="col-1 align-self-center" id="course_action">';
                    $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                    $result .= '</div>';
                    $result .= '</div>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
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
                    ->get();

            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>';
            $result .= '</div>';
            foreach($course_reviewer as $row){
                $result .= '<div class="col-12 row align-self-center" id="course_list">';
                $result .= '<div class="col-11 row align-self-center" style="border:0px solid black;margin: 0px;">';
                $result .= '<div class="checkbox_style align-self-center">';
                $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
                $result .= '</div>';
                $result .= '<a href="/E_Portfolio/list/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">';
                $result .= '<div class="col-1 align-self-center" id="course_image">';
                $result .= '<img src="'.url('image/portfolio.png').'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col-11 align-self-center" id="course_name">';
                $result .= '<p id="file_name"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                $result .= '</div>';
                $result .= '</a>';
                $result .= '</div>';
                $result .= '<div class="col-1 align-self-center" id="course_action">';
                $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>';
                $result .= '</div>';
                $result .= '</div>';
            }
        }
        return $result;
    }

    public function downloadZipFiles($course_id,$download)
    {
        if($download=="checked"){
            $string = explode('---',$course_id);
        }

        $name = "E - Portfolio Zip Files";
        $zip = new ZipArchive;
        $fileName = storage_path('private/E-Portfolio/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        for($i=0;$i<=(count($string)-1);$i++){
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
                      ->where('faculty.faculty_id','=',$faculty_id)
                      ->get();


            foreach($course as $row){
                $syllabus = $row->syllabus;
                $faculty_name = $row->faculty_name;
                $programme_name = $row->programme_name;
                $subject_code = $row->subject_code;
                $subject_name = $row->subject_name;
                $lecture_name = $row->name;
                $year = $row->year;
                $semester = $row->semester;
            }

            $assessments = DB::table('assessments')
                        ->select('assessments.*')
                        ->where('course_id', '=', $string[$i])
                        ->where('status', '=', 'Active')
                        ->orderBy('assessments.assessment_name')
                        ->get();

            $assessment_list = DB::table('assessment_list')
                        ->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                        ->select('assessment_list.*','assessments.*')
                        ->where('assessment_list.status', '=', 'Active')
                        ->where('assessments.course_id', '=', $string[$i])
                        ->get();

            $lecturer_result = DB::table('assessment_result_students')
                     ->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
                     ->select('assessment_result_students.*','assessments.*')
                     ->where('assessments.course_id', '=', $string[$i])
                     ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                     ->where('assessment_result_students.status','=','Active')
                     ->groupBy('assessment_result_students.student_id')
                     ->groupBy('assessments.ass_id')
                     ->get();

            $ass_final = DB::table('ass_final')
                        ->select('ass_final.*')
                        ->where('course_id', '=', $string[$i])
                        ->where('status', '=', 'Active')
                        ->orderBy('ass_final.assessment_name')
                        ->get();

            $assessment_final = DB::table('assessment_final')
                        ->join('ass_final','ass_final.fx_id','=','assessment_final.fx_id')
                        ->select('assessment_final.*','ass_final.*')
                        ->where('ass_final.course_id', '=', $string[$i])
                        ->where('assessment_final.status', '=', 'Active')
                        ->get();

            $lecturer_fx_result = DB::table('assessment_final_result')
                     ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                     ->join('users','users.user_id', '=', 'students.user_id')
                     ->select('assessment_final_result.*','students.*','users.*')
                     ->where('assessment_final_result.course_id', '=', $string[$i])
                     ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                     ->where('assessment_final_result.status','=','Active')
                     ->groupBy('assessment_final_result.student_id')
                     ->get();

            $action = DB::table('action_v_a')
                        ->select('action_v_a.*')
                        ->where('course_id', '=', $string[$i])
                        ->where('status', '=', 'Approved')
                        ->get();

            $ca_action = DB::table('actionca_v_a')
                        ->select('actionca_v_a.*')
                        ->where('course_id', '=', $string[$i])
                        ->where('status', '=', 'Verified')
                        ->get();

            $fa_action = DB::table('actionfa_v_a')
                        ->select('actionfa_v_a.*')
                        ->where('course_id', '=', $string[$i])
                        ->where('status', '=', 'Approved')
                        ->get();

            $lecture_note = DB::table('lecture_notes')
                        ->select('lecture_notes.*')
                        ->where('course_id', '=', $string[$i])
                        ->where('status', '=', 'Active')
                        ->get();

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
            $table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('E-PORTFOLIO FORM'),null,$noSpaceAndCenter);
            $table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
            $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(null, $cellRowContinue);
            $table->addCell(null, $cellRowContinue);
            $table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
            $table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

            $textrun = $header->addTextRun();
            $textrun->addText("",null,$noSpaceAndCenter);

            $CA_full_title = $section->addText('E-PORTFOLIO FORM',array('bold' => true),$noSpaceAndCenter);

            $textrun = $section->addTextRun();
            $textrun->addText("",null,$noSpaceAndCenter);

            $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
            $phpWord->addTableStyle('title', $styleTable);
            $title = $section->addTable('title');
            // $section->addTextBreak(1);
            $title->addRow();
            $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Course Information',array('bold' => true),$noSpaceAndCenter);

            $textrun = $section->addTextRun();
            $textrun->addText("",null,$noSpaceAndCenter);

            $styleTable = array('cellMargin' => 60);
            $fontStyle = array('bold' => true);
            $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
            $phpWord->addTableStyle('Course Table', $styleTable);
            $course_table = $section->addTable('Course Table');
            $styleCell = array('valign' => 'center','borderSize' => 6);
            $cellColSpan = array('gridSpan' => '2','valign' => 'bottom');
            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Faculty', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText($faculty_name, null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Programme', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText($programme_name, null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Subject Code', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText($subject_code, null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Subject Name', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText($subject_name, null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Course Coordinator', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText($lecture_name, null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Lecturer Name', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText($lecture_name, null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Academic Year / Semester', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText('20'.$year."_".$semester, null, $noSpaceAndLeft);

            $course_table->addRow(500);
            $course_table->addCell(12000,$cellColSpan)->addText('(Lecturer to decide their own creativity to fill up this section)', null, $noSpaceAndLeft);

            $course_table->addRow(1);
            $course_table->addCell(3000,$styleCell)->addText('Introduction to Reader', null, $noSpaceAndLeft);
            $course_table->addCell(9000,$styleCell)->addText("- To brief summary of specific teaching experiences and qualifications to date.<w:br/>- Intro lecturer's portfolio, organization, items and etc.<w:br/>- Sign and date", null, $noSpaceAndLeft);

            $textrun = $section->addTextRun();
            $textrun->addText("",null,$noSpaceAndCenter);

            $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
            $phpWord->addTableStyle('title', $styleTable);
            $title = $section->addTable('title');
            // $section->addTextBreak(1);
            $title->addRow();
            $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('E - Portfolio',array('bold' => true),$noSpaceAndCenter);

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
            $cellColSpan = array('gridSpan' => 3,'valign' => 'center','bgColor' => 'cccccc');
            $cellRowNorSpan = array('vMerge' => 'restart','valign' => 'top');
            $cellRowNorContinue = array('vMerge' => 'continue','valign' => 'center');
            $table->addRow(1);
            $table->addCell(2000,$cellRowSpan)->addText('Folder Name',$fontStyle, $noSpaceAndCenter);
            $table->addCell(4000,$cellRowSpan)->addText("Documents", $fontStyle, $noSpaceAndCenter);
            $table->addCell(6000,$cellColSpan)->addText('Softcopy',$fontStyle, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowContinue);
            $table->addCell(4000,$cellRowContinue);
            $table->addCell(2000,$styleThCell)->addText('Course Coordinator',$fontStyle, $noSpaceAndCenter);
            $table->addCell(2000,$styleThCell)->addText('Moderator',$fontStyle, $noSpaceAndCenter);
            $table->addCell(2000,$styleThCell)->addText('Audit Committee',$fontStyle, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorSpan)->addText('1. Course Information',$fontStyle,$noSpaceAndLeft);
            $table->addCell(4000,$styleCell)->addText('a) Syllabus',Null,$noSpaceAndLeft);
            if($syllabus!=""){
                $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
            }else{
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            }
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('b) Teaching Plan',Null,$noSpaceAndLeft);
            if(count($action)>0){
                $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
            }else{
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            }
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('c) Internal Moderation of Continuous Assessment',Null,$noSpaceAndLeft);
            if(count($ca_action)>0){
                $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
            }else{
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            }
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('d) Internal Moderation of Final Examination Paper',Null,$noSpaceAndLeft);
            if(count($fa_action)>0){
                $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
            }else{
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            }
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('e) External Moderation of Final Examination Paper ( if application )',Null,$noSpaceAndLeft);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('f) CA Marks with Excel Format (by programme)',Null,$noSpaceAndLeft);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('g) Final Marks with Excel Format (by programme)',Null,$noSpaceAndLeft);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('h) Timetable',Null,$noSpaceAndLeft);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorContinue);
            $table->addCell(4000,$styleCell)->addText('i) Attendance',Null,$noSpaceAndLeft);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorSpan)->addText('2. Teaching Material',$fontStyle,$noSpaceAndLeft);
            $table->addCell(4000,$styleCell)->addText('Lecture slides, documents and other teaching materials',Null,$noSpaceAndLeft);
            if(count($lecture_note)>0){
                $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
            }else{
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            }
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $num = 3;
            foreach($assessments as $row){

                $table->addRow(1);
                $table->addCell(2000,$cellRowNorSpan)->addText($num.'. '.$row->assessment_name,$fontStyle,$noSpaceAndLeft);
                $table->addCell(4000,$styleCell)->addText('a) Moderated Question(s)',Null,$noSpaceAndLeft);
                $question = 0;
                foreach($assessment_list as $row_list){
                    if($row->ass_id == $row_list->ass_id){
                        if($row_list->ass_type=="Question"){
                            $question++;
                        }
                    }
                }
                if($question>0){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

                $table->addRow(1);
                $table->addCell(3000,$cellRowNorContinue);
                $table->addCell(3000,$styleCell)->addText('b) Moderated Marking Scheme(s) / Solution(s)',Null,$noSpaceAndLeft);
                $solution = 0;
                foreach($assessment_list as $row_list){
                    if($row->ass_id == $row_list->ass_id){
                        if($row_list->ass_type=="Solution"){
                            $solution++;
                        }
                    }
                }
                if($solution>0){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

                $table->addRow(1);
                $table->addCell(3000,$cellRowNorContinue);
                $table->addCell(3000,$styleCell)->addText('c) Samples ( 9 samples : 3 Good; 3 Average; 3 poor )',Null,$noSpaceAndLeft);
                $result = 0;
                foreach($lecturer_result as $row_result){
                    if($row->ass_id == $row_result->ass_id){
                        $result++;
                    }
                }
                if($result>=9){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $num++;
            }   

            if(count($ass_final)>0){
                $table->addRow(1);
                $table->addCell(2000,$cellRowNorSpan)->addText($num.'. '.$row->assessment_name,$fontStyle,$noSpaceAndLeft);
                $table->addCell(4000,$styleCell)->addText('a) Moderated Examination Paper',Null,$noSpaceAndLeft);
                $f_q = 0;
                if(count($assessment_final)>0){
                    foreach($assessment_final as $row_final){
                        if($row_final->ass_fx_type=="Question"){
                            $f_q++;
                        }
                    }
                }
                if($f_q>0){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

                $table->addRow(1);
                $table->addCell(3000,$cellRowNorContinue);
                $table->addCell(3000,$styleCell)->addText('b) Moderated Examination Paper Marking Scheme',Null,$noSpaceAndLeft);
                $f_s = 0;
                if(count($assessment_final)>0){
                    foreach($assessment_final as $row_final){
                        if($row_final->ass_fx_type=="Solution"){
                            $f_s++;
                        }
                    }
                }
                if($f_s>0){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

                $table->addRow(1);
                $table->addCell(3000,$cellRowNorContinue);
                $table->addCell(3000,$styleCell)->addText('c) Final Exam Scripts Moderation for Course File Form',Null,$noSpaceAndLeft);
                if(count($fa_action)>0){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

                $table->addRow(1);
                $table->addCell(3000,$cellRowNorContinue);
                $table->addCell(3000,$styleCell)->addText('d) Samples ( 9 samples : 3 Good; 3 Average; 3 poor )',Null,$noSpaceAndLeft);
                if(count($lecturer_fx_result)>=9){
                    $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                }else{
                    $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                }
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
                $num++;
            }

            $table->addRow(1);
            $table->addCell(2000,$cellRowNorSpan)->addText($num.'. Course Review Report',$fontStyle,$noSpaceAndLeft);
            $table->addCell(4000,$styleCell)->addText('a) Completed course review report',Null,$noSpaceAndLeft);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

            $textrun = $section->addTextRun();
            $textrun->addText("",null,$noSpaceAndCenter);

            $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
            $fontStyle = array('bold' => true);
            $phpWord->addTableStyle('Sign Table', $styleTable);
            $table = $section->addTable('Sign Table');
            $styleCell = array('valign' => 'center');
            $table->addRow(1000);
            $table->addCell(4000)->addText('Lecturer: ',null, $noSpaceAndLeft);
            $table->addCell(4000)->addText('Verified By Head Of Department: ', null, $noSpaceAndLeft);
            $table->addCell(4000)->addText('Approved By Dean: ', null, $noSpaceAndLeft);

            $hod = DB::table('staffs')
                     ->join('users','staffs.user_id','=','users.user_id')
                     ->select('staffs.*','users.*')
                     ->where('users.position', '=', 'HoD')
                     ->where('staffs.department_id','=',$department_id)
                     ->get();

            $dean = DB::table('staffs')
                     ->join('users','staffs.user_id','=','users.user_id')
                     ->select('staffs.*','users.*')
                     ->where('users.position', '=', 'Dean')
                     ->where('staffs.faculty_id','=',$faculty_id)
                     ->get();

            $table->addRow(1);
            $table->addCell(4000)->addText('Name: '.$lecture_name,null, $noSpaceAndLeft);
            $table->addCell(4000)->addText('Name: '.$hod[0]->name, null, $noSpaceAndLeft);
            $table->addCell(4000)->addText('Name: '.$dean[0]->name, null, $noSpaceAndLeft);

            $table->addRow(1);
            $table->addCell(4000)->addText('Date: '.date("Y-j-n"),null, $noSpaceAndLeft);
            $table->addCell(4000)->addText('Date: ',null, $noSpaceAndLeft);
            $table->addCell(4000)->addText('Date: ',null, $noSpaceAndLeft);

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($subject_code." ".$subject_name.'.docx');
            $zip->addFile($subject_code." ".$subject_name.'.docx',$subject_code." ".$subject_name.'.docx');
        }
        $zip->close();
        for($i=0;$i<=(count($string)-1);$i++){
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
                      ->where('faculty.faculty_id','=',$faculty_id)
                      ->get();
            foreach($course as $row){
                $syllabus = $row->syllabus;
                $faculty_name = $row->faculty_name;
                $programme_name = $row->programme_name;
                $subject_code = $row->subject_code;
                $subject_name = $row->subject_name;
                $lecture_name = $row->name;
                $year = $row->year;
                $semester = $row->semester;
                File::delete($subject_code." ".$subject_name.'.docx');
            }
        }
        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    public function viewListE_Portfolio($id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();

        $syllabus = $course[0]->syllabus;

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $assessment_list = DB::table('assessment_list')
                    ->join('assessments','assessments.ass_id','=','assessment_list.ass_id')
                    ->select('assessment_list.*','assessments.*')
                    ->where('assessment_list.status', '=', 'Active')
                    ->where('assessments.course_id', '=', $id)
                    ->get();

        $lecturer_result = DB::table('assessment_result_students')
                 ->join('assessments','assessments.ass_id','=','assessment_result_students.ass_id')
                 ->select('assessment_result_students.*','assessments.*')
                 ->where('assessments.course_id', '=', $id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->groupBy('assessments.ass_id')
                 ->get();

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

        $assessment_final = DB::table('assessment_final')
                    ->join('ass_final','ass_final.fx_id','=','assessment_final.fx_id')
                    ->select('assessment_final.*','ass_final.*')
                    ->where('ass_final.course_id', '=', $id)
                    ->where('assessment_final.status', '=', 'Active')
                    ->get();

        $lecturer_fx_result = DB::table('assessment_final_result')
                 ->join('students','students.student_id', '=', 'assessment_final_result.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_final_result.*','students.*','users.*')
                 ->where('assessment_final_result.course_id', '=', $id)
                 ->where('assessment_final_result.submitted_by','=', 'Lecturer')
                 ->where('assessment_final_result.status','=','Active')
                 ->groupBy('assessment_final_result.student_id')
                 ->get();

        $action = DB::table('action_v_a')
                    ->select('action_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $ca_action = DB::table('actionca_v_a')
                    ->select('actionca_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Verified')
                    ->get();

        $fa_action = DB::table('actionfa_v_a')
                    ->select('actionfa_v_a.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Approved')
                    ->get();

        $lecture_note = DB::table('lecture_notes')
                    ->select('lecture_notes.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->get();

        return view('dean.E_portfolio.E_Portfolio_View_List',compact('course','assessments','assessment_list','lecturer_result','ass_final','assessment_final','lecturer_fx_result','syllabus','action','ca_action','fa_action','lecture_note'));
    }
}

