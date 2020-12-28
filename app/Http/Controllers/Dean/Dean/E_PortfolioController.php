<?php

namespace App\Http\Controllers\Dean\Dean;

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
        $department_id = $staff_dean->department_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;

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

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        $attendance = $this->viewAttendance($id);

        return view('dean.Reviewer.E_portfolio.viewE_Portfolio',compact('course','assessments','assessment_list','lecturer_result','ass_final','timetable','attendance','assessment_final','lecturer_fx_result','syllabus','action','ca_action','fa_action','lecture_note'));
	}

	public function Download_E_Portfolio($id)
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

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        $attendance = $this->viewAttendance($id);

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
    	if(count($timetable)>0){
            $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
    	$table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);

    	$table->addRow(1);
    	$table->addCell(2000,$cellRowNorContinue);
    	$table->addCell(4000,$styleCell)->addText('i) Attendance',Null,$noSpaceAndLeft);
    	if(round($attendance)>80){
            $table->addCell(2000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
            $table->addCell(2000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
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
	}

    public function viewAttendance($id)
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
                 ->where('courses.course_id', '=', $id)
                 ->get();

        $assign_student = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$id)
                    ->where('assign_student_course.status','=',"Active")
                    ->orderBy('students.batch')
                    ->get();

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->select('attendance.*','timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->orderBy('attendance.A_date')
                    ->orderBy('timetable.class_hour')
                    ->get();

        if($course[0]->semester =='A'){
            $weeks = 7;
            $startDate = $course[0]->startDate;
        }else{
            $weeks = 14;
            $startDate = $course[0]->startDate;
        }
        $absent_person = 0;
        $timetable_count = 0;
        $total_tt = 0;
        $take_hour = 0;
        for($i=1;$i<=$weeks;$i++){
            $count_hour = 0;
            if($i==1){
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($startDate . $week));
                    $hour = explode(',',$row->class_hour);
                    $count_hour = $count_hour + count($hour);
                    $take_hour = 0;
                    foreach($attendance as $att_row){
                        if($att_row->A_week==$i){
                            $s_hour = explode('-',$att_row->hour);
                            $e_hour = explode('-',$att_row->hour);
                            $last_hour = $this->getFullTime($s_hour[0],$e_hour[1]);
                            $timetable_hour = $att_row->class_hour;
                            $explode_th = explode(',',$timetable_hour);
                            $sperate = explode(',',$last_hour);
                            $less_hour = $att_row->less_hour;
                            if(count($sperate)>count($explode_th)){
                                $less_hour = count($explode_th)-count($sperate);
                            }
                                if($less_hour==0){
                                    for($s=0;$s<=count($sperate)-1;$s++){
                                        $take_hour++;
                                    }
                                }else if($less_hour<0){
                                    for($s=0;$s<=count($explode_th)-1;$s++){
                                        $take_hour++;
                                    }
                                }else{
                                    $take_hour= $take_hour + $less_hour;
                                }
                        }
                    }
                }
                $total_tt        = $total_tt+$take_hour;
                $timetable_count = $timetable_count + $count_hour;
            }else{
                $startDate = strtotime($course[0]->startDate);
                $add_date = $startDate+(($i-1)*(86400*7));
                $add_startDate = date('Y-m-d',$add_date);
                foreach($timetable as $row){
                    $week = "Next ".$row->week;
                    $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
                    $hour = explode(',',$row->class_hour);
                    $take_hour = 0;
                    if($row->F_or_H=="Full"){
                        $count_hour = $count_hour + count($hour);
                    }else{
                        if ($i % 2) {
                            $count_hour = $count_hour + count($hour);
                        }
                    }
                    foreach($attendance as $att_row){
                        if($att_row->A_week==$i){
                            $s_hour = explode('-',$att_row->hour);
                            $e_hour = explode('-',$att_row->hour);
                            $last_hour = $this->getFullTime($s_hour[0],$e_hour[1]);
                            $timetable_hour = $att_row->class_hour;
                            $explode_th = explode(',',$timetable_hour);
                            $sperate = explode(',',$last_hour);
                            $less_hour = $att_row->less_hour;
                            if(count($sperate)>count($explode_th)){
                                $less_hour = count($explode_th)-count($sperate);
                            }
                                if($less_hour==0){
                                    for($s=0;$s<=count($sperate)-1;$s++){
                                        $take_hour++;
                                    }
                                }else if($less_hour<0){
                                    for($s=0;$s<=count($explode_th)-1;$s++){
                                        $take_hour++;
                                    }
                                }else{
                                    $take_hour= $take_hour + $less_hour;
                                }
                        }
                    }
                }
                $total_tt        = $total_tt + $take_hour;
                $timetable_count = $timetable_count + $count_hour;    
            }
        }
        $completed = 0;

        if($timetable_count!=0){
            $completed = ($total_tt/$timetable_count)*100;
        }
        
        return $completed;
    }

    public function getFullTime($s_hour,$e_hour){
        $s_hour = intval($s_hour);
        $e_hour = intval($e_hour);
        $hour = $e_hour - $s_hour;
        $zero = "0";
        if($s_hour>=1000){
            $zero = "";
        }
        $f_time = "";
        $current = "";
        for($time = 100;$time<=$hour;$time=$time+100){
            if($current==""){
                $current = intval($s_hour+100);
                if($current>=1000){
                    $f_time .= $zero.$s_hour."-".($s_hour+100);
                }else{
                    $f_time .= $zero.$s_hour."-0".($s_hour+100);
                }
            }else{
                $zero = "0";
                if($current>=1000){
                    $zero = "";
                }
                $added_hour = intval($current+100);
                if($added_hour>=1000){
                    $f_time .= ",".$zero.$current."-".$added_hour;
                }else{
                    $f_time .= ",".$zero.$current."-0".$added_hour;
                }
                $current = $added_hour;
            }
        }
        return $f_time;
    }
}

