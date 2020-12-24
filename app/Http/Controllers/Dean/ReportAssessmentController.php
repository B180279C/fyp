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
	public function DownloadAssessmentReport($id)
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
	                 ->get();

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

	$name = "Moderation Form (CA) Zip Files";
    $zip = new ZipArchive;
    $fileName = storage_path('private/Assessment/Zip_Files/'.$name.'.zip');
    $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    for($c=0;$c<(count($string)-1);$c++){
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