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
use App\ActionFA_V_A;

class M_FinalExamController extends Controller
{
	public function ModeratorFinalExam($id)
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

        $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

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

        $action = DB::table('actionfa_v_a')
                  ->select('actionfa_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('actionFA_id')
                  ->get();

        if(count($course)>0){
            return view('dean.Moderator.Final_Exam.M_FinalExamList',compact('course','ass_final','TP_Ass','tp','action'));
        }else{
            return redirect()->back();
        }
	}

	public function M_FX_Moderate_Action(Request $request)
	{
		$actionFA_id = $request->get('actionFA_id');
		$course_id =  $request->get('course_id');
		$degree = "";
		for($i = 1; $i<=12;$i++){
			$degree .= $i."_".$request->get('degree'.$i)."///";
		}

		$remark = "";
		$count_assessemnt = $request->get('count_assessemnt');
		for($c = 1;$c<=$count_assessemnt;$c++){
			$fx_id  = $request->get('fx_id_'.$c);
			$r_u = $request->get('r_u_'.$c);
			$a_a = $request->get('a_a_'.$c);
			$e_c = $request->get('e_c_'.$c);
			$remark .= $fx_id."<???>".$request->get('remark_'.$c)."%-PER-%".$r_u.",".$a_a.",".$e_c."///NextAss///";
		}

		$action = ActionFA_V_A::where('actionFA_id', '=', $actionFA_id)->firstOrFail();
		$action->degree  = $degree;
		$action->suggest = $remark;
		$action->feedback = $request->get('feedback');
		$action->status = "Waiting For Rectification";
		$action->for_who = "Lecturer";
		$action->save();
    return redirect()->back()->with('success','Final Examination Moderation Form Created Successfully');
	}

  public function ModerationFormReport($actionFA_id)
  {
    $user_id       = auth()->user()->user_id;
    $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
    $faculty_id    = $staff_dean->faculty_id;

    $action = ActionFA_V_A::where('actionFA_id', '=', $actionFA_id)->firstOrFail();

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

    $ass_final = DB::table('ass_final')
                    ->select('ass_final.*')
                    ->where('course_id', '=', $action->course_id)
                    ->where('status', '=', 'Active')
                    ->orderBy('ass_final.assessment_name')
                    ->get();

    $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $action->course_id)
                  ->get();

    $weightage = 0;
    foreach($ass_final as $row){
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
      $table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('INTERNAL/EXTERNAL MODERATION OF FINAL EXAMINATION PAPER'),null,$noSpaceAndCenter);
      $table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
      $table->addCell(2500)->addText("",null,$noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(null, $cellRowContinue);
      $table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
      $table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

      $textrun = $header->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $FA_full_title = $section->addText('INTERNAL/EXTERNAL MODERATION OF FINAL EXAMINATION QUESTION PAPER <w:br/> FINAL EXAMINATION / RESIT EXAMINATION',array('bold' => true),$noSpaceAndCenter);

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
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part B : Distribution of topic and ( CLO )',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);


      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('topic Table', $styleTable);
      $table = $section->addTable('topic Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc');
      $table->addRow(1);
      $table->addCell(3000,$styleCellTH)->addText('Question No.',$fontStyle, $noSpaceAndCenter);
      $table->addCell(4000,$styleCellTH)->addText("Topic(s) covered", $fontStyle, $noSpaceAndCenter);
      $table->addCell(6000,$styleCellTH)->addText('Course Learning Outcome (s) covered',$fontStyle, $noSpaceAndCenter);
      $table->addCell(3000,$styleCellTH)->addText("Bloom's Taxanomy Level*", $fontStyle, $noSpaceAndCenter);

      foreach($ass_final as $row){
        $table->addRow(1);
        $table->addCell(3000,$styleCell)->addText($row->assessment_name,Null, $noSpaceAndCenter);
        $table->addCell(3000,$styleCell)->addText($row->topic,Null, $noSpaceAndCenter);
        $table->addCell(3000,$styleCell)->addText($row->CLO,Null, $noSpaceAndCenter);
        $CLO_sel = explode(',',$row->CLO);
        $domain_level = "";
        for($i = 0; $i<=count($CLO_sel)-1;$i++){
          $num = 1;
          foreach($TP_Ass as $row_ass){
            if(('CLO'.$num) == $CLO_sel[$i]){
              $domain_level .= $row_ass->domain_level.',';
            }
            $num++;
          }
        }
        $table->addCell(3000,$styleCell)->addText($domain_level,Null, $noSpaceAndCenter);
      }

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('CLO Table', $styleTable);
      $table = $section->addTable('CLO Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc');
      $num = 1;
      foreach ($TP_Ass as $row_ass){
        $table->addRow(1);
        $table->addCell(3000,$styleCell)->addText('CLO '.$num,Null, $noSpaceAndCenter);
        $table->addCell(9000,$styleCell)->addText($row_ass->CLO."<w:br/>( ".$row_ass->domain_level.' , '.$row_ass->PO." ) ",Null, $noSpaceAndLeft);
        $num++;
      }

      $section->addPageBreak();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part C : Indicate the degree to which moderator agree or disagree',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);


      $degree = explode('///',$action->degree);
      $styleCell = array('valign' => 'center');
      $styleThCell = array('valign' => 'center','bgColor' => 'cccccc');
      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $phpWord->addTableStyle('degree Table', $styleTable);
      $table = $section->addTable('degree Table');
      $cellRowSpan = array('vMerge' => 'restart','valign' => 'center','bgColor' => 'cccccc');
      $cellRowContinue = array('vMerge' => 'continue','valign' => 'center','bgColor' => 'cccccc');
      $cellColSpan = array('gridSpan' => 7,'valign' => 'center');
      $table->addRow(1);
      $table->addCell(1000,$cellRowSpan)->addText('No',$fontStyle, $noSpaceAndCenter);
      $table->addCell(6000,$cellRowSpan)->addText("", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("5", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("4", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("3", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("2", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("1", $fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$cellRowContinue);
      $table->addCell(6000,$cellRowContinue);
      $table->addCell(1000,$styleThCell)->addText("Strongly <w:br/> Agree", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Agree", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Neutral", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Disagree", $fontStyle, $noSpaceAndCenter);
      $table->addCell(1000,$styleThCell)->addText("Strongly Disagree", $fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$cellColSpan)->addText('QUESTION PAPER',$fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('1',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions are within the scope of the course syllabus and are aligned to the mapped CLOs and PLOs",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[0]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('2',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions are arranged according to complexity from lower difficult level to higher difficulty level",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[1]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('3',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("None of the questions in the examination questions paper are overlap.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[2]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('4',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Question are free from factual errors.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[3]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('5',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions are free from racial/ethnic, religious, sexual and political bias and other sensitive issues.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[4]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('6',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Optional questions (if any) are equivalent in terms of CLO and marks awarded (if applicable).",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[5]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('7',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Questions and the descriptions are simple and clear.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[6]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('8',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Scientific / technical terminologies are relevant to the course.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[7]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('9',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Labels and descriptions used for diagrams, tables and figures are clear and consistent.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[8]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$cellColSpan)->addText('MARKING SCHEME',$fontStyle, $noSpaceAndCenter);

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('1',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Marks(s) stated in the marking scheme are based on the examination paper set.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[9]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('2',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Answer for each question is correct and appropriate to CLOs.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[10]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $table->addRow(1);
      $table->addCell(1000,$styleCell)->addText('3',Null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText("Total marks for each question and/or section for the whole examination paper correctly calculated and stated.",Null, $noSpaceAndLeft);
      for($i = 1;$i<=5;$i++){
        $degree_result = explode('_',$degree[11]);
        if($degree_result[1]==$i){
          $table->addCell(1000,$styleCell)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(1000,$styleCell)->addText('',Null, $noSpaceAndCenter);
        }
      }

      $section->addPageBreak();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part D : Suggestion for improvement',array('bold' => true),$noSpaceAndCenter);

      foreach($ass_final as $row){
        $full_suggest = explode('///NextAss///',$action->suggest);
        for($n = 0;$n<=(count($full_suggest)-1);$n++){
            $getFxId = explode('<???>',$full_suggest[$n]);
            if($getFxId[0]==$row->fx_id){
              $suggest_list = explode('%-PER-%',$getFxId[1]);
              $percentage = explode(',',$suggest_list[1]);
            }
        }
        $textrun = $section->addTextRun();
        $textrun->addText("",null,$noSpaceAndCenter);
        $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
        $fontStyle = array('bold' => true);
        $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
        $phpWord->addTableStyle('suggest Table', $styleTable);
        $table = $section->addTable('suggest Table');
        $styleCell = array('valign' => 'center');
        $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2);
        $cellColSpan = array('gridSpan' => 2,'valign' => 'center');

        $table->addRow(1);
        $table->addCell(12000,$styleCellTH)->addText($row->assessment_name,$fontStyle, $noSpaceAndLeft);
        $table->addRow(1);
        $suggest = $table->addCell(12000,$cellColSpan);
        $html = str_replace("<br>","<br/>",$suggest_list[0]);
        \PhpOffice\PhpWord\Shared\Html::addHtml($suggest,$html,false);

        $table->addRow(1);
        $table->addCell(9000,$styleCell)->addText('Percentage of work involving remembering and understanding %',Null, $noSpaceAndLeft);
        $table->addCell(3000,$styleCell)->addText($percentage[0].'%',Null, $noSpaceAndRight);

        $table->addRow(1);
        $table->addCell(9000,$styleCell)->addText('Percentage of work involving application &amp; analysis %',Null, $noSpaceAndLeft);
        $table->addCell(3000,$styleCell)->addText($percentage[1].'%',Null, $noSpaceAndRight);

        $table->addRow(1);
        $table->addCell(9000,$styleCell)->addText('Percentage of work involving evaluation and creation %',Null, $noSpaceAndLeft);
        $table->addCell(3000,$styleCell)->addText($percentage[2].'%',Null, $noSpaceAndRight);

        $table->addRow(1);
        $table->addCell(9000,$styleCell)->addText('Total',Null, $noSpaceAndLeft);
        $table->addCell(3000,$styleCell)->addText('100%',Null, $noSpaceAndRight);
      }

      $section->addPageBreak();

      $styleTable = array('borderSize' => 6, 'borderColor' => 'black');
      $phpWord->addTableStyle('title', $styleTable);
      $title = $section->addTable('title');
      // $section->addTextBreak(1);
      $title->addRow();
      $title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part E : Any Other Feedback',array('bold' => true),$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);
      $styleTable = array('borderBottomSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('feedback Table', $styleTable);
      $table = $section->addTable('feedback Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $feedback = $table->addCell(12000);
      $html = str_replace("<br>","<br/>",$action->feedback);
      \PhpOffice\PhpWord\Shared\Html::addHtml($feedback,'<p><b>Feedback: </b></p>'.$html,false);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);
      $styleTable = array('borderSize' => 6, 'cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('Sign Table', $styleTable);
      $table = $section->addTable('Sign Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $table->addCell(5000)->addText('Internal / External Moderator:',array('bold' => true),$noSpaceAndCenter);
      $table->addRow(2000);
      $table->addCell(5000,$styleCell)->addText('',array('bold' => true),$noSpaceAndCenter);
      $table->addRow(1);
      $table->addCell(5000)->addText('Name: '.$Moderator[0]->name, null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(5000)->addText('Date: ', null, $noSpaceAndLeft);

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('Self Table', $styleTable);
      $table = $section->addTable('Self Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6,'borderTopSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderTopSize' => 6,'borderRightSize' => 6,'gridSpan' => 2))->addText('Self-declaration',$fontStyle,$noSpaceAndLeft);
      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderRightSize' => 6,'gridSpan' => 2))->addText('I hereby declared that the Final Examination Question Paper has been moderated by Internal / External Moderator and I have corrected all the amendments according to the comments from Internal / External Moderator.',Null,$noSpaceAndLeft);
      $table->addRow(1000);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(5000,array('borderBottomSize' => 6))->addText(' ', null, $noSpaceAndLeft);
      $table->addCell(7000,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(5000)->addText('Name: '.$Moderator[0]->name, null, $noSpaceAndLeft);
      $table->addCell(7000,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(5000)->addText('Date: ', null, $noSpaceAndLeft);
      $table->addCell(7000,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(12000,array('borderBottomSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 3))->addText('',$fontStyle,$noSpaceAndLeft);

      $section->addPageBreak();

      $textrun = $section->addTextRun();
      $textrun->addText("",null,$noSpaceAndCenter);

      $styleTable = array('cellMargin' => 60);
      $fontStyle = array('bold' => true);
      $noSpaceAndLeft = array('spaceAfter' => 0,'align'=>'left');
      $phpWord->addTableStyle('A_V Table', $styleTable);
      $table = $section->addTable('A_V Table');
      $styleCell = array('valign' => 'center');
      $styleCellTH = array('valign' => 'center','bgColor' => 'cccccc','gridSpan' => 2); 
      $table->addRow(1);
      $table->addCell(12000,array('borderTopSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 5))->addText('Approval and Verification by Head of Department &amp; Dean',$fontStyle,$noSpaceAndCenter);
      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderRightSize' => 6,'gridSpan' => 4))->addText('Comments from Head of Department (if any): ',Null,$noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderBottomSize' => 6,'gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(1000,array('gridSpan' => 2))->addText('Signature if Head of Department', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addText(' ', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Name: ', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Date: ', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(12000,array('borderBottomSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 5))->addText('',$fontStyle,$noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderRightSize' => 6,'gridSpan' => 4))->addText('Comments from Dean (if any): ',Null,$noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('borderBottomSize' => 6,'gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(1000,array('borderSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(3000)->addText('Approval For Printing', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(12000,array('gridSpan' => 3))->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(100,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(1000,array('gridSpan' => 2))->addText('Signature if Dean', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(500);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('borderBottomSize' => 6,'gridSpan' => 2))->addText(' ', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Name: ', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addRow(1);
      $table->addCell(50,array('borderLeftSize' => 6))->addText('', null, $noSpaceAndLeft);
      $table->addCell(4000,array('gridSpan' => 2))->addText('Date: ', null, $noSpaceAndLeft);
      $table->addCell(7000)->addText('', null, $noSpaceAndLeft);
      $table->addCell(100,array('borderRightSize' => 6))->addText('', null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(12000,array('borderBottomSize' => 6,'borderLeftSize' => 6,'borderRightSize' => 6,'gridSpan' => 5))->addText('',$fontStyle,$noSpaceAndLeft);

      $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
      $objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
      return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
  }
}