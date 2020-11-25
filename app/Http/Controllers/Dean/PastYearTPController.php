<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Student;
use App\Staff;
use App\Assessments;
use App\AssessmentList;
use App\AssessmentResultStudent;
use App\Imports\syllabusRead;
// use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;
use File;

class PastYearTPController extends Controller
{
	public function PastYearTP($id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        $previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','!=',$id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

        if(count($course)>0){
            return view('dean.PastYearTP.viewPYTP',compact('course','previous_semester'));
        }else{
            return redirect()->back();
        }
	}

	public function PastYearTPDownload($id,$course_id)
	{
		$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

		$course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('programmes','subjects.programme_id','=','programmes.programme_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs','staffs.id','=','courses.lecturer')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','programmes.*','staffs.*','users.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        $previous_semester = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                    ->join('staffs','staffs.id','=','courses.lecturer')
                    ->join('users','staffs.user_id','=','users.user_id')
                    ->select('subjects.*','courses.*','semesters.*','staffs.*','users.*')
                    ->where('subjects.subject_id', '=', $course[0]->subject_id)
                    ->where('courses.course_id','=',$course_id)
                    ->where('courses.status', '=', 'Active')
                    ->orderByDesc('semesters.semester_name')
                    ->groupBy('courses.course_id')
                    ->get();

        $TP = DB::table('teaching_plan')
        	->select('teaching_plan.*')
        	->where('teaching_plan.course_id','=',$course_id)
        	->get();

        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

       	$path = storage_path('private/syllabus/'.$course[0]->syllabus);
        $array = (new syllabusRead)->toArray($path);
        $CLO = "";
        for($i=0;$i<(count($array[0]));$i++){
        	if($array[0][$i][2]=="Synopsis :"){
        		$synopsis = str_replace("•", "<w:br/>•", $array[0][$i][3]);
        	}
        	$str = strval($array[0][$i][2]);
        	if((str_contains($str, 'CLO'))&&($array[0][$i][1]==null)&&($array[0][$i][3]!=null)&&($array[0][$i][15]==null)){
        		if($CLO == ""){
        			$CLO .= $array[0][$i][2].": ".$array[0][$i][3];
        		}else{
					$CLO .= "<w:br/>".$array[0][$i][2].": ".$array[0][$i][3];
        		}
        	}
        	if((str_contains($str, 'References'))&&($array[0][$i][1]!=null)&&($array[0][$i][8]!=null)){
        		$references = str_replace("• Additional", "<w:br/>• Additional", $array[0][$i][8]);
        	}
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
		$table->addCell(4000, $cellRowSpan)->addText(htmlspecialchars('TEACHING PLAN'),null,$noSpaceAndCenter);
		$table->addCell(2200)->addText("Eff. Date",null,$noSpaceAndCenter);
		$table->addCell(2500)->addText("",null,$noSpaceAndCenter);

		$table->addRow(1);
		$table->addCell(null, $cellRowContinue);
		$table->addCell(null, $cellRowContinue);
		$table->addCell(2200)->addText("Page No",array('align' => 'both'),$noSpaceAndCenter);
		$table->addCell(2500)->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'),null,$noSpaceAndCenter);

		$textrun = $header->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);

		$teaching_plan_full_title = $section->addText('TEACHING PLAN',array('bold' => true),$noSpaceAndCenter);

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
		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('1.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Course Code &amp; Course Title: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($course[0]->subject_code." : ".$course[0]->subject_name, null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('2.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Year of Study (Programme): ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText('Year 1 and Year 2 ('.$course[0]->programme_name.')' , null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('3.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Credit Hour: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText('', null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('4.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Lecturer: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($course[0]->name."( ".$course[0]->staff_id." )", null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('5.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Tutor: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($course[0]->name."( ".$course[0]->staff_id." )", null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('6.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Year and Trimester: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($course[0]->semester_name, null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('7.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Synopsis: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($synopsis, null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('8.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('Course Learning Outcomes (CLO): ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($CLO, null, $noSpaceAndLeft);

		$course_table->addRow(1);
		$course_table->addCell(500,$styleCell)->addText('9.',null, $noSpaceAndLeft);
		$course_table->addCell(2000,$styleCell)->addText('References: ', null, $noSpaceAndLeft);
		$course_table->addCell(10000,$styleCell)->addText($references, null, $noSpaceAndLeft);

		$section->addPageBreak();
		
		$styleTable = array('borderSize' => 6, 'borderColor' => 'black');
		$phpWord->addTableStyle('title', $styleTable);
		$title = $section->addTable('title');
		// $section->addTextBreak(1);
		$title->addRow();
		$title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part B : Methods of Assessment',array('bold' => true),$noSpaceAndCenter);

		$textrun = $section->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);

        $all_assessment = explode('///',$TP_Ass[0]->assessment);
        $assessment = explode(',',$all_assessment[0]);
        $assessment_num = explode(',',$all_assessment[1]);

		$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
		$fontStyle = array('bold' => true);
		$phpWord->addTableStyle('Fancy Table', $styleTable);
		$table = $section->addTable('Fancy Table');
		$cellRowSpan = array('vMerge' => 'restart','valign' => 'center','bgColor' => 'cccccc');
		$cellRowContinue = array('vMerge' => 'continue','valign' => 'center','bgColor' => 'cccccc');
		$cellColSpan = array('gridSpan' => (count($assessment)-1),'valign' => 'center','bgColor' => 'cccccc');
		$table->addRow(1);
		$table->addCell(500,$cellRowSpan)->addText('NO',array('bold' => true), $noSpaceAndCenter);
		$table->addCell(800,$cellRowSpan)->addText('CO', $fontStyle, $noSpaceAndCenter);
		$table->addCell(500,$cellRowSpan)->addText('Programme Outcomes (PO)', $fontStyle, $noSpaceAndCenter);
		$table->addCell(500,$cellRowSpan)->addText('Domain &amp; Taxonomy Level', $fontStyle, $noSpaceAndCenter);
		$table->addCell(500,$cellRowSpan)->addText('Teaching Methods', $fontStyle, $noSpaceAndCenter);
		$table->addCell(8000,$cellColSpan)->addText('Assessment Methods &amp; Mark Breakdown', $fontStyle, $noSpaceAndCenter);

		$table->addRow(1);
		$table->addCell(500,$cellRowContinue);
		$table->addCell(800,$cellRowContinue);
		$table->addCell(1000,$cellRowContinue);
		$table->addCell(1000,$cellRowContinue);
		$table->addCell(1000,$cellRowContinue);
		for($m = 0;$m<(count($assessment)-1);$m++){
			$table->addCell(2000,array('bgColor' => 'cccccc'))->addText($assessment[$m], $fontStyle, $noSpaceAndCenter);
		}
		

		$table->addRow(1);
		$table->addCell(500,$cellRowContinue);
		$table->addCell(800,$cellRowContinue);
		$table->addCell(1000,$cellRowContinue);
		$table->addCell(1000,$cellRowContinue);
		$table->addCell(1000,$cellRowContinue);
		for($n = 0;$n<(count($assessment_num)-1);$n++){
			$table->addCell(2000,array('bgColor' => 'cccccc'))->addText($assessment_num[$n]."%", $fontStyle, $noSpaceAndCenter);
		}

		$num = 1;
		foreach($TP_Ass as $row){
			$table->addRow(1);
			$table->addCell(500)->addText($num,null, $noSpaceAndCenter);
			$table->addCell(800)->addText($row->CLO, null, $noSpaceAndCenter);
			$table->addCell(1000)->addText($row->PO, null, $noSpaceAndCenter);
			$table->addCell(1000)->addText($row->domain_level, null, $noSpaceAndCenter);
			$method = str_replace(",", ",<w:br/>", $row->method);
			$table->addCell(1000)->addText($method, null, $noSpaceAndCenter);			
			$check = explode(',',$row->markdown);
			for($c = 0; $c<=($n-1);$c++){
				if($check[$c]!=""){
					$table->addCell(2000)->addText('Yes', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
				}else{
					$table->addCell(2000)->addText('No', array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
				}
			}
			$num++;
		}

		$cellColSpanFull = array('gridSpan' => (5+count($assessment)-1),'valign' => 'center','bgColor' => '#d9d9d9');
		$table->addRow(1);
		$table->addCell(12000,$cellColSpanFull)->addText("*Domain -- Affective (A), Cognitive (C), Psychomotor (P); Taxonomy Level - A(Level 1-5), C(Level 1-6), P(Level 1-5).*<w:br/>*All COs must be assessed by at least one assessment method (ensure that the only assessment method is not an optional choice).*<w:br/>*Individual breakdown of marks for an assessment method (i.e. one assessment question / part mapped to only one CO) is not necessary in the teaching plan. Individual breakdown of marks is only required when preparing the assessment moderation form.*", null, $noSpaceAndLeft);

		$section->addPageBreak();

		$styleTable = array('borderSize' => 6, 'borderColor' => 'black');
		$phpWord->addTableStyle('title', $styleTable);
		$title = $section->addTable('title');
		$title->addRow();
		$title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part C : Continual Quality Improvement (CQI)',array('bold' => true),$noSpaceAndCenter);
		$textrun = $section->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);

		$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
		$fontStyle = array('bold' => true);
		$phpWord->addTableStyle('CQI table', $styleTable);
		$table = $section->addTable('CQI table');
		$styleCell = array('valign' => 'center');
		$table->addRow(1);
		$table->addCell(600,$styleCell)->addText('No',array('bold' => true), $noSpaceAndCenter);
		$table->addCell(6000,$styleCell)->addText('Proposed Improvement Action(s)<w:br/>(from previous trimester Course Report)', $fontStyle, $noSpaceAndCenter);
		$table->addCell(6000,$styleCell)->addText('Plan for this Trimester<w:br/>(action(s) must be shown in Part D, if applicable)<w:br/>(to be transferred to this trimester Course Report)', $fontStyle, $noSpaceAndCenter);

		$table->addRow(5);
		$table->addCell(600,$styleCell)->addText('1',null, $noSpaceAndCenter);
		$table->addCell(6000,$styleCell)->addText('', null, $noSpaceAndLeft);
		$table->addCell(6000,$styleCell)->addText('', null, $noSpaceAndLeft);

		$table->addRow(5);
		$table->addCell(600,$styleCell)->addText('2',null, $noSpaceAndCenter);
		$table->addCell(6000,$styleCell)->addText('', null, $noSpaceAndLeft);
		$table->addCell(6000,$styleCell)->addText('', null, $noSpaceAndLeft);

		$textrun = $section->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);
		$styleTable = array('borderSize' => 6, 'borderColor' => 'black');
		$phpWord->addTableStyle('title', $styleTable);
		$title = $section->addTable('title');
		$title->addRow();
		$title->addCell(12000,array('bgColor' => 'cccccc'))->addText('Part D : Weekly Plan',array('bold' => true),$noSpaceAndCenter);
		$textrun = $section->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);
		$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
		$fontStyle = array('bold' => true);
		$phpWord->addTableStyle('Fancy Table', $styleTable);
		$table = $section->addTable('Fancy Table');
		$styleCell = array('valign' => 'center');
		$table->addRow(1,array('tblHeader' => true));
		$table->addCell(600,$styleCell)->addText('Week',array('bold' => true), $noSpaceAndCenter);
		$table->addCell(5200,$styleCell)->addText('Lecture Topic <w:br/> (including sub-topics)', $fontStyle, $noSpaceAndCenter);
		$table->addCell(800,$styleCell)->addText('Lecture <w:br/> (F2F) Hour', $fontStyle, $noSpaceAndCenter);
		$table->addCell(1500,$styleCell)->addText('Tutorial / Practical', $fontStyle, $noSpaceAndCenter);
		$table->addCell(1800,$styleCell)->addText('Assessment', $fontStyle, $noSpaceAndCenter);
		$table->addCell(2000,$styleCell)->addText('Remarks <w:br/> (CQI Action / Activity)', $fontStyle, $noSpaceAndCenter);


		foreach($TP as $row){
			$cellRowSpan = array('vMerge' => 'restart');
			$cellRowContinue = array('vMerge' => 'continue','valign' => 'center');
			$topic = DB::table('plan_topics')
       			->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
       			->select('plan_topics.*','teaching_plan.*')
       			->where('plan_topics.tp_id','=',$row->tp_id)
       			->get();
       		$i = 0;
       		foreach($topic as $row_topic){
       			$table->addRow(null);
       			if($i==0){
       				$table->addCell(600,$cellRowSpan)->addText($row->week,array( 'bold'=>true ), $noSpaceAndCenter);
       			}else{
       				$table->addCell(null,$cellRowContinue);
       			}
				$L_topic = $table->addCell(5200);
				\PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$row_topic->lecture_topic.'</b>',false);
				$html = str_replace("<br>","<br/>",$row_topic->sub_topic);

				\PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,$html,false);
				$table->addCell(800)->addText($row_topic->lecture_hour,null,$noSpaceAndCenter);
				if($i==0){
					$tutorial = $table->addCell(1500,$cellRowSpan);
					$html_t = str_replace("<br>","<br/>",$row->tutorial);
					\PhpOffice\PhpWord\Shared\Html::addHtml($tutorial,"<span style='text-align:center'>".$html_t."</span>",false);
					$assessment = $table->addCell(1800,$cellRowSpan);
					$html_a = str_replace("<br>","<br/>",$row->assessment);
					\PhpOffice\PhpWord\Shared\Html::addHtml($assessment,"<span style='text-align:center'>".$html_a."</span>",false);
					$remark = $table->addCell(2000,$cellRowSpan);
					$html_r = str_replace("<br>","<br/>",$row->remarks);
					\PhpOffice\PhpWord\Shared\Html::addHtml($remark,"<span style='text-align:center'>".$html_r."</span>",false);
				}else{
					$table->addCell(null,$cellRowContinue);
					$table->addCell(null,$cellRowContinue);
					$table->addCell(null,$cellRowContinue);
				}
				$i++;
       		}
		}

		$textrun = $section->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);
		$textrun = $section->addTextRun();
		$textrun->addText("",null,$noSpaceAndCenter);

		$section->addText('This Teaching Plan is: ');
		$styleTable = array('borderSize' => 6, 'cellMargin' => 60);
		$fontStyle = array('bold' => true);
		$phpWord->addTableStyle('Sign Table', $styleTable);
		$table = $section->addTable('Sign Table');
		$styleCell = array('valign' => 'center');
		$table->addRow(1000);
		$table->addCell(4000)->addText('Prepared By:',null, $noSpaceAndLeft);
		$table->addCell(4000)->addText('Moderated By:', null, $noSpaceAndLeft);
		$table->addCell(4000)->addText('Approved By:', null, $noSpaceAndLeft);

		$table->addRow(1);
		$table->addCell(4000)->addText('Name:'.$course[0]->name.'<w:br/>Course Coordinator',null, $noSpaceAndLeft);
		$table->addCell(4000)->addText('Name:<w:br/>Moderator', null, $noSpaceAndLeft);
		$table->addCell(4000)->addText('Name:<w:br/>Head of Department', null, $noSpaceAndLeft);

		$table->addRow(1);
		$table->addCell(4000)->addText('Date:',null, $noSpaceAndLeft);
		$table->addCell(4000)->addText('Date:',null, $noSpaceAndLeft);
		$table->addCell(4000)->addText('Date:',null, $noSpaceAndLeft);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save('helloWorld.docx');
		return response()->download(public_path('helloWorld.docx'))->deleteFileAfterSend(true);

		// $phpWord = new TemplateProcessor('word/word.docs');
  //       $phpWord->setValue('id','AAA'); 
  //       $phpWord->setValue('name','BBB'); 
  //       $phpWord->('email',url('image/img_icon.png'));
  //       $filename = $course_id;
  //       $phpWord->saveAs($filename.'.docs');
  //       return response()->download($filename.'.docs')->deleteFileAfterSend(true);
	}
}