<?php

namespace App\Http\Controllers\Dean\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Staff;
use App\Subject;
use App\Department;
use App\Faculty;
use App\Imports\syllabusRead;
use App\TP_Assessment_Method;
use App\ActionCA_V_A;

class M_AssessmentController extends Controller
{
	public function ModeratorAssessment($id)
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

        if(count($course)>0){
            return view('dean.Moderator.Assessment.M_AssessmentList',compact('course','assessments','TP_Ass','action'));
        }else{
            return redirect()->back();
        }
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
		$action->save();
        return redirect()->back()->with('success','Continuous Assessment Moderation Form Created Successfully');
	}

	public function ModerationFormReport($actionCA_id)
	{
		$user_id       = auth()->user()->user_id;
	    $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
	    $faculty_id    = $staff_dean->faculty_id;

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

        $assessments = DB::table('assessments')
                    ->select('assessments.*')
                    ->where('course_id', '=', $action->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('assessments.assessment_name')
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
	    $cellColSpan = array('gridSpan' => count($assessments),'valign' => 'center','bgColor' => 'cccccc');
	    $table->addRow(1);
	    $table->addCell(6000,$cellRowSpan)->addText('Course Learning Outcome covered',$fontStyle, $noSpaceAndCenter);
    	$table->addCell(6000,$cellColSpan)->addText("Continuous Assessment", $fontStyle, $noSpaceAndCenter);

    	$table->addRow(1);
	    $table->addCell(6000,$cellRowContinue);
    	foreach($assessments as $row){
    		$table->addCell((6000/count($assessments)),$styleThCell)->addText($row->assessment_name,$fontStyle, $noSpaceAndCenter);
    	}
    	$num = 1;
    	foreach($TP_Ass as $row_tp){
    		$table->addRow(1);
    		$table->addCell(6000,$styleCell)->addText('CLO '.$num." : ".$row_tp->CLO."<w:br/>( ".$row_tp->domain_level.' , '.$row_tp->PO." ) ",null, $noSpaceAndLeft);
    		foreach($assessments as $row){
    			$check = false;
                $CLO = $row->CLO;
                $CLO_sel = explode('///',$CLO);
                $CLO_List = explode(',',$CLO_sel[0]);
                for($i = 0;$i<=count($CLO_List)-1;$i++){
                	if($CLO_List[$i]==('CLO'.$num)){
                		$check = true;
                   	}
               }
               if($check==true){
               	$table->addCell((6000/count($assessments)),$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
               }else{
				$table->addCell((6000/count($assessments)),$styleCell)->addText('N', array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
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
	    $cellColSpan_HOD = array('gridSpan' => count($assessments)*2,'valign' => 'center');
	    $table->addRow(1);
	    $table->addCell(6000,$cellColSpan)->addText('Assessment',array('bold' => true), $noSpaceAndRight);
	    foreach($assessments as $row){
    		$table->addCell((6000/count($assessments)),$cellColSpan)->addText($row->assessment_name,$fontStyle, $noSpaceAndCenter);
    	}
    	$table->addRow(1);
	    $table->addCell(6000,$cellColSpan)->addText('% of Coursework',array('bold' => true), $noSpaceAndRight);
	    foreach($assessments as $row){
    		$table->addCell((6000/count($assessments)),$cellColSpan)->addText($row->coursework."%",$fontStyle, $noSpaceAndCenter);
    	}
    	$table->addRow(1);
    	$table->addCell(500,$styleCell)->addText('',array('bold' => true), $noSpaceAndRight);
	    $table->addCell(5500,$styleCell)->addText('A = Accepted &amp; R = Rectification',array('bold' => true), $noSpaceAndRight);
	    foreach($assessments as $row){
    		$table->addCell((6000/(count($assessments))/2),$styleCell)->addText('A',$fontStyle, $noSpaceAndCenter);
    		$table->addCell((6000/(count($assessments))/2),$styleCell)->addText('R',$fontStyle, $noSpaceAndCenter);
    	}

    	$num = 1;
    	foreach ($TP_Ass as $row_tp){
    		$table->addRow(1);
    		$table->addCell(500,$styleCell)->addText($num,array('bold' => true), $noSpaceAndCenter);
    		$table->addCell(5500,$styleCell)->addText('CLO '.$num." : ".$row_tp->CLO."<w:br/>( ".$row_tp->domain_level.' , '.$row_tp->PO." ) ",null, $noSpaceAndLeft);
    		foreach($assessments as $row){
    			$check = false;
                $Acc = false;
                $rec = false;
                $AccOrRec_list = explode('///',$action->AccOrRec);
                for($m = 0;$m<=(count($AccOrRec_list)-1);$m++){
                  $AorR = explode('::',$AccOrRec_list[$m]);
                  if($AorR[0]=="CLO_".$num."_".$row->ass_id){
                    $check = true;
                    if($AorR[1]=="A"){
                      $Acc = true;
                    }else{
                      $rec = true;
                    }
                  }
                }
                if($check==true){
                	if($Acc==true){
                		$table->addCell((6000/(count($assessments))/2),$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                		$table->addCell((6000/(count($assessments))/2),$styleCell)->addText('', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                	}
                	if($rec==true){
                		$table->addCell((6000/(count($assessments))/2),$styleCell)->addText('', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
                		$table->addCell((6000/(count($assessments))/2),$styleCell)->addText('Y',array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
                	}
	            }else{
	            	$table->addCell((6000/(count($assessments))/2),$cellColSpan)->addText('',null, $noSpaceAndCenter);
	            }
	    	}
    		$num++;
    	}
    	$table->addRow(1);
    	$table->addCell(6000,$cellColSpan_NoColor)->addText('Signature of Internal Moderator',array('bold' => true), $noSpaceAndRight);
	    foreach($assessments as $row){
    		$table->addCell((6000/count($assessments)),$cellColSpan_NoColor)->addText('',$fontStyle, $noSpaceAndCenter);
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

    	foreach($assessments as $row){
    		$textrun = $section->addTextRun();
    		$textrun->addText("",null,$noSpaceAndLeft);
    		$full_suggest = explode('///NextAss///',$action->suggest);
            for($n = 0;$n<=(count($full_suggest)-1);$n++){
                $getAssId = explode('<???>',$full_suggest[$n]);
               	if($getAssId[0]==$row->ass_id){
                    $suggest_list = $getAssId[1];
               	}
           	}
           	$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
		    $fontStyle = array('bold' => true);
		    $phpWord->addTableStyle($row->assessment_name.'table', $styleTable);
		    $table = $section->addTable($row->assessment_name.'table');
		    $styleCell = array('valign' => 'center');
		    $table->addRow(1);
		    $table->addCell(12000)->addText($row->assessment_name,array('bold' => true),$noSpaceAndLeft);
		    $table->addRow(1);
		    $suggest = $table->addCell(12000);
		    $html = str_replace("<br>","<br/>",$suggest_list);
	        \PhpOffice\PhpWord\Shared\Html::addHtml($suggest,'Suggestion(s): '.$html,false);
    	}

		$textrun = $section->addTextRun();
    	$textrun->addText("",null,$noSpaceAndLeft);

    	$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
		$fontStyle = array('bold' => true);
		$phpWord->addTableStyle('Sign table', $styleTable);
		$table = $section->addTable('Sign table');
		$styleCell = array('valign' => 'center');
		$table->addRow(1);
		$table->addCell(6000,$styleCell)->addText('Internal Moderator : ',array('bold' => true),$noSpaceAndCenter);
    	$table->addCell(6000,$styleCell)->addText('Verified by Head of department : ',array('bold' => true),$noSpaceAndCenter);
    	$table->addRow(1000);
    	$table->addCell(6000,$styleCell)->addText('',array('bold' => true),$noSpaceAndCenter);
		$table->addCell(6000,$styleCell)->addText('',array('bold' => true),$noSpaceAndCenter);

		$table->addRow(1);
	    $table->addCell(6000)->addText('Name: '.$Moderator[0]->name, null, $noSpaceAndLeft);
	    $table->addCell(6000)->addText('Name: ', null, $noSpaceAndLeft);

	    $table->addRow(1);
	    $table->addCell(6000)->addText('Date: ',null, $noSpaceAndLeft);
	    $table->addCell(6000)->addText('Date: ',null, $noSpaceAndLeft);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
		return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
	}	
}