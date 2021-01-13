<?php

namespace App\Http\Controllers\Dean\Moderator;

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
use App\TP_Assessment_Method;
use ZipArchive;
use File;
use App\ActionCA_V_A;

class M_AssessmentController extends Controller
{
	public function ModeratorAssessment($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
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
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $action = DB::table('actionca_v_a')
                  ->select('actionca_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('actionCA_id')
                  ->get();

        $action_big = DB::table('actionca_v_a')
                  ->select('actionca_v_a.*')
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


        $group_assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->groupBy('assessment')
                    ->get();

        if(count($course)>0){
            return view('dean.Moderator.Assessment.M_AssessmentList',compact('course','assessments','all_assessments','TP_Ass','action','moderator_person_name','verified_person_name','action_big','group_assessments'));
        }else{
            return redirect()->back();
        }
	}

    public static function getQuestion($course_id,$question)
    {
        $sample_stored = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $course_id)
                    ->where('assessment', '=', $question)
                    ->where('status', '=', 'Active')
                    ->groupBy('sample_stored')
                    ->get();
        return $sample_stored;
    }

	public function M_Ass_Moderate_Action(Request $request)
	{
		$actionCA_id = $request->get('actionCA_id');
		$count_CLO = $request->get('count_CLO');
		$course_id =  $request->get('course_id');

		$assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();
        $AccOrRec = "";
		for($i = 1;$i<=$count_CLO;$i++){
			foreach ($assessments as $row) {
				if($request->get('CLO_'.$i.'_'.$row->ass_id)!=Null){
					$AccOrRec .= 'CLO_'.$i.'_'.$row->ass_id."::".$request->get('CLO_'.$i.'_'.$row->ass_id)."///";
				}
			}
		}
		$remark = "";
		for($c = 1;$c<=(count($assessments));$c++){
			$ass_id  = $request->get('ass_id_'.$c);
			$remark .= $ass_id."<???>".$request->get('remark_'.$c)."///NextAss///";
		}
		$action = ActionCA_V_A::where('actionCA_id', '=', $actionCA_id)->firstOrFail();
		$action->AccOrRec  = $AccOrRec;
		$action->suggest = $remark;
		$action->status = "Waiting For Rectification";
		$action->for_who = "Lecturer";
		$action->moderator_date = date("Y-n-j");
		$action->save();
        return redirect()->back()->with('success','Continuous Assessment Moderation Form Created Successfully');
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
	                 ->where('courses.moderator', '=', $staff_dean->id)
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

	public function viewAssessment($id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;

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

        $group_assessments = DB::table('assessments')
                            ->select('assessments.*')
                            ->where('course_id', '=', $id)
                            ->where('status', '=', 'Active')
                            ->groupBy('assessments.assessment')
                            ->get();

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $action = DB::table('actionca_v_a')
                  ->select('actionca_v_a.*')
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
            return view('dean.Moderator.Assessment.viewAssessment',compact('course','assessments','group_assessments','action','moderator_person_name','verified_person_name'));
        }else{
            return redirect()->back();
        }
    }

    public static function getCoursework($course_id,$question)
    {

        $sample_stored = DB::table('assessments')
                            ->select('assessments.*')
                            ->where('course_id', '=', $course_id)
                            ->where('assessment', '=', $question)
                            ->where('status', '=', 'Active')
                            ->groupBy('sample_stored')
                            ->get();
        foreach($sample_stored as $row){
            $assessment_list = DB::table('assessment_list')
                            ->select('assessment_list.*')
                            ->where('assessment_list.ass_id', '=', $row->ass_id)
                            ->where('assessment_list.status', '=', 'Active')
                            ->groupBy('assessment_list.ass_id')
                            ->get();
            if(count($assessment_list)>0){
                return "true";
            }else{
                return "false";
            }
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
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $id)
                 ->get();

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
                    ->get();

        $group_assessments = DB::table('assessments')
                            ->select('assessments.*')
                            ->where('course_id', '=', $id)
                            ->where('status', '=', 'Active')
                            ->groupBy('assessments.assessment')
                            ->get();

        $lecturer_result = DB::table('assessment_result_students')
                 ->join('assessments', 'assessments.ass_id', '=', 'assessment_result_students.ass_id')
                 ->select('assessment_result_students.student_id','assessments.assessment')
                 ->where('assessments.course_id', '=', $id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.ass_id')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        $ass_id = array();
        $count = array();
        foreach ($group_assessments as $group) {
            $sample_stored = DB::table('assessments')
                            ->select('assessments.*')
                            ->where('course_id', '=', $id)
                            ->where('assessment', '=', $group->assessment)
                            ->where('status', '=', 'Active')
                            ->groupBy('sample_stored')
                            ->orderBy('assessments.assessment_name')
                            ->get();
            foreach($sample_stored as $ss){
                array_push($ass_id,$ss->ass_id);
            }
            array_push($count,[count($sample_stored),$sample_stored[0]->assessment]);
        }

        $assessment_list = DB::table('assessment_list')
                    ->join('assessments', 'assessments.ass_id', '=', 'assessment_list.ass_id')
                    ->select('assessment_list.ass_id','assessments.assessment')
                    ->where('assessments.course_id', '=', $id)
                    ->where('assessment_list.status', '=', 'Active')
                    ->groupBy('assessment_list.ass_id')
                    ->get();

        if(count($course)>0){
            $path = storage_path('private/syllabus/'.$course[0]->syllabus);
            $array = (new syllabusRead)->toArray($path);
            return response()->json([$array[0],$assessments,$assessment_list,$ass_id,$count,$lecturer_result]);
            // return response()->json($array[0]);
        }else{
            return redirect()->back();
        }    
    }

    public function create_assessment_list($id,$coursework,$question)
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
                    ->groupBy('sample_stored')
                    ->get();

        if(count($course)>0){
            return view('dean.Moderator.Assessment.createAssList',compact('course','mark','question','assessments','previous_semester','group_assessments','TP_Ass','coursework','sample_stored'));
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
                    ->groupBy('sample_stored')
                    ->get();

        if(count($course)>0){
            return view('dean.Moderator.Assessment.createQuestion',compact('course','mark','question','assessments','previous_semester','group_assessments','TP_Ass','coursework','sample_stored'));
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
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $course_id)
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
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $assessments->course_id)
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
            return view('dean.Moderator.Assessment.AssessmentListView', compact('course','assessments','question','group_list','assessment_list'));
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
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $assessments->course_id)
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
            if(count($assessment_list)==0){
                return redirect()->back()->with('failed','The question is empty.');
            }else{
                return view('dean.Moderator.Assessment.viewWholePaper', compact('assessments','assessment_list','question'));
            }
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
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*')
                 ->where('courses.moderator', '=', $staff_dean->id)
                 ->where('courses.course_id', '=', $assessments->course_id)
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
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

        $result = "";
        if($value!=""){
            $assessments = DB::table('assessments')
                        ->select('assessments.*')
                        ->where('assessments.sample_stored','LIKE','%'.$value.'%')
                        ->where('assessments.assessment', '=', $question)
                        ->where('assessments.status', '=', 'Active')
                        ->where('assessments.course_id','=',$course_id)
                        ->groupBy('assessments.sample_stored')
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
                    $result .= '<a href="'.$character.'/Moderator/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->sample_stored.'</b></p>';
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
                    ->groupBy('assessments.sample_stored')
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
                    $result .= '<a href="'.$character.'/Moderator/assessment/view_list/'.$row->ass_id.'" class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/file.png').'" width="20px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->sample_stored.'</b></p>';
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

        if(auth()->user()->position=="Dean"){
            $character = '';
        }else if(auth()->user()->position=="HoD"){
            $character = '/hod';
        }else if(auth()->user()->position=="Lecturer"){
            $character = '/lecturer';
        }

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
                            $result .= '<a href="'.$character.'/Moderator/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->sample_stored.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='.$character."/Moderator/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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
                            $result .= '<a href="'.$character.'/Moderator/images/assessment/'.$row->ass_document.'" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$course[0]->semester_name.' : '.$assessments->sample_stored.' / '.$row_group->ass_type.' / '.$row->ass_name.' <br> <a href='.$character."/Moderator/assessment/view/whole_paper/".$row->ass_id.' class='."full_question".' target='."_blank".'>Whole paper</a>">';
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