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

class ReportTPController extends Controller
{
  public function viewTPDetail($id)
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

        $verified_by = Staff::where('id', '=', $course[0]->moderator)->firstOrFail();
        $verified_person_name = User::where('user_id', '=', $verified_by->user_id)->firstOrFail();

        $approved_by = Staff::where('id', '=', $course[0]->verified_by)->firstOrFail();
        $approved_person_name = User::where('user_id', '=', $approved_by->user_id)->firstOrFail();

        $TP = DB::table('teaching_plan')
          ->select('teaching_plan.*')
          ->where('teaching_plan.course_id','=',$id)
          ->get();

        $topic = DB::table('plan_topics')
            ->join('teaching_plan', 'teaching_plan.tp_id', '=', 'plan_topics.tp_id')
            ->select('plan_topics.*','teaching_plan.*')
            ->where('teaching_plan.course_id','=',$id)
            ->get();
            
        $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $id)
                  ->get();

        $TP_CQI = DB::table('tp_cqi')
                  ->select('tp_cqi.*')
                  ->where('course_id', '=', $id)
                  ->where('tp_cqi.status','=','Active')
                  ->get();

        $action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderBy('action_id')
                  ->get();

        if(count($course)>0){
            return view('dean.Report.viewTeachingPlan',compact('course','TP','topic','TP_Ass','TP_CQI','action','verified_person_name','approved_person_name'));
        }else{
            return redirect()->back();
        }
  }

  public function getTPaction($course_id)
  {
    $course_tp_action = DB::table('action_v_a')
                  ->join('courses','courses.course_id','=','action_v_a.course_id')
                  ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                  ->join('staffs', 'staffs.id','=','courses.lecturer')
                  ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                  ->select('action_v_a.*','courses.*','subjects.*','staffs.*','users.*','action_v_a.status as action_status')
                  ->where('courses.course_id','=',$course_id)
                  ->where('courses.status','=','Active')
                  ->orderByDesc('action_v_a.action_id')
                  ->first();

    $status = "Pending";
    if($course_tp_action!=""){
      $status = $course_tp_action->action_status;
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
                    $status = $this->getTPaction($row->course_id);
                    if($status == "Rejected"){
                      $color = "red";
                    }else if($status == "Approved"){
                      $color = "green";
                    }else if($status == "Pending"){
                      $color = "grey";
                    }else{
                      $color = "blue";
                    }
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-10 row align-self-center" style="border:0px solid black;margin: 0px;">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    if($status!="Pending"){
                      $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
                    }else{
                      $result .= '<input type="hidden" value="'.$row->course_id.'">';
                    }
                    $result .= '</div>';
                    if($status!="Pending"){
                      $result .= '<a href="'.$character.'/report/TP/view/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">';
                    }else{
                      $result .= '<a id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;" onclick="showMessage()">';
                    }
                    $result .= '<div class="col-1 align-self-center" id="course_image">';
                    $result .= '<img src="'.url('image/plan.png').'" width="25px" height="25px"/>';
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
                $status = $this->getTPaction($row->course_id);
                if($status == "Rejected"){
                  $color = "red";
                }else if($status == "Approved"){
                  $color = "green";
                }else if($status == "Pending"){
                  $color = "grey";
                }else{
                  $color = "blue";
                }
                $result .= '<div class="col-12 row align-self-center" id="course_list">';
                $result .= '<div class="col-10 row align-self-center" style="border:0px solid black;margin: 0px;">';
                $result .= '<div class="checkbox_style align-self-center">';
                if($status!="Pending"){
                  $result .= '<input type="checkbox" value="'.$row->course_id.'" class="group_q group_download">';
                }else{
                  $result .= '<input type="hidden" value="'.$row->course_id.'">';
                }
                $result .= '</div>';

                if($status!="Pending"){
                  $result .= '<a href="'.$character.'/report/TP/view/'.$row->course_id.'" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">';
                }else{
                  $result .= '<a id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;" onclick="showMessage()">';
                }
                $result .= '<div class="col-1 align-self-center" id="course_image">';
                $result .= '<img src="'.url('image/plan.png').'" width="25px" height="25px"/>';
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
  public function DownloadTPReport($id)
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

      $TP = DB::table('teaching_plan')
            ->select('teaching_plan.*')
            ->where('teaching_plan.course_id','=',$id)
            ->get();

      $TP_Ass = DB::table('tp_assessment_method')
                ->select('tp_assessment_method.*')
                ->where('course_id', '=', $id)
                ->get();

      $TP_CQI = DB::table('tp_cqi')
                ->select('tp_cqi.*')
                ->where('course_id', '=', $id)
                ->where('status','=','Active')
                ->get();

      $action = DB::table('action_v_a')
                  ->select('action_v_a.*')
                  ->where('course_id', '=', $id)
                  ->orderByDesc('action_id')
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
    $course_table->addCell(10000,$styleCell)->addText($course[0]->credit, null, $noSpaceAndLeft);

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
          $table->addCell(2000)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
        }else{
          $table->addCell(2000)->addText('N', array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
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

    $num = 1;
    foreach($TP_CQI as $row){
      $table->addRow(5);
      $table->addCell(600,$styleCell)->addText($num,null, $noSpaceAndCenter);
      $table->addCell(6000,$styleCell)->addText($row->action, null, $noSpaceAndLeft);
      $table->addCell(6000,$styleCell)->addText($row->plan, null, $noSpaceAndLeft);
      $num++;
    }

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
        $lecture_topic = "";
        if($row_topic->lecture_topic!=""){
          $lecture_topic = explode('///',$row_topic->lecture_topic);
          \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$lecture_topic[1].'</b>',false);
        }else{
          \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$row_topic->lecture_topic.'</b>',false);
        }
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
    $Moderator = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id', '=', $course[0]->moderator)
                 ->get();

    $verified_by = DB::table('staffs')
                 ->join('users','staffs.user_id','=','users.user_id')
                 ->select('staffs.*','users.*')
                 ->where('staffs.id','=',$course[0]->verified_by)
                 ->get();

    $table->addRow(1);
    if($action[0]->prepared_date!=NULL){
      if($course[0]->staff_sign!=NULL){
        $s_p = storage_path('/private/staffSign/'.$course[0]->staff_sign);
        $table->addCell(4000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
      }else{
        $table->addCell(4000,$styleCell)->addText($course[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
      }
    }else{
      $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
    }

    if($action[0]->verified_date!=NULL){
      if($Moderator[0]->staff_sign!=NULL){
        $s_m = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
        $table->addCell(4000)->addImage($s_m,array('width'=>80, 'height'=>40, 'align'=>'center'));
      }else{
        $table->addCell(4000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
      }
    }else{
      $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
    }

    if($action[0]->approved_date!=NULL){
      if($verified_by[0]->staff_sign!=NULL){
        $s_v = storage_path('/private/staffSign/'.$verified_by[0]->staff_sign);
        $table->addCell(4000)->addImage($s_v,array('width'=>80, 'height'=>40, 'align'=>'center'));
      }else{
        $table->addCell(4000,$styleCell)->addText($verified_by[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
      }
    }else{
      $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
    }

    $table->addRow(1);
    $table->addCell(4000)->addText('Prepared By : '.$course[0]->name.'<w:br/>Course Coordinator',null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Moderated By: '.$Moderator[0]->name.'<w:br/>Moderator', null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Approved By : '.$verified_by[0]->name.'<w:br/>'.$verified_by[0]->position, null, $noSpaceAndLeft);

    $table->addRow(1);
    $table->addCell(4000)->addText('Date: '.$action[0]->prepared_date,null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Date: '.$action[0]->verified_date,null, $noSpaceAndLeft);
    $table->addCell(4000)->addText('Date: '.$action[0]->approved_date,null, $noSpaceAndLeft);

    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($course[0]->subject_code." ".$course[0]->subject_name.'.docx');
    return response()->download(public_path($course[0]->subject_code." ".$course[0]->subject_name.'.docx'))->deleteFileAfterSend(true);
  }

  public function ZipFilesDownloadTPReport($course_id,$download)
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

    $name = "Teaching Plan Zip Files";
    $zip = new ZipArchive;
    $fileName = storage_path('private/Teaching_Plan/Zip_Files/'.$name.'.zip');
    $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    for($i=0;$i<(count($string)-1);$i++){
      
      $course = DB::table('courses')
                     ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                     ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                     ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                     ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                     ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                     ->join('staffs', 'staffs.id','=','courses.lecturer')
                     ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                     ->select('courses.*','subjects.*','semesters.*','staffs.*','users.*','programmes.*','faculty.*')
                     ->where('courses.course_id', '=', $string[$i])
                     ->get();

    foreach($course as $row){
      $credit = $row->credit;
      $staff_id = $row->staff_id;
            $syllabus = $row->syllabus;
            $programme_name = $row->programme_name;
            $subject_code = $row->subject_code;
            $subject_name = $row->subject_name;
            $lecture_name = $row->name;
            $year = $row->year;
            $semester = $row->semester;
            $semester_name = $row->semester_name;
        }

    $TP = DB::table('teaching_plan')
            ->select('teaching_plan.*')
            ->where('teaching_plan.course_id','=',$string[$i])
            ->get();

      $TP_Ass = DB::table('tp_assessment_method')
                  ->select('tp_assessment_method.*')
                  ->where('course_id', '=', $string[$i])
                  ->get();

      $TP_CQI = DB::table('tp_cqi')
                  ->select('tp_cqi.*')
                  ->where('course_id', '=', $string[$i])
                  ->where('status','=','Active')
                  ->get();

      $action = DB::table('action_v_a')
                    ->select('action_v_a.*')
                    ->where('course_id', '=', $string[$i])
                    ->orderByDesc('action_id')
                    ->get();

      $path = storage_path('private/syllabus/'.$syllabus);
        $array = (new syllabusRead)->toArray($path);
        $CLO = "";
        $synopsis = "";
        $references = "";
        for($m=0;$m<(count($array[0]));$m++){
          if($array[0][$m][2]=="Synopsis :"){
            $synopsis = str_replace("•", "<w:br/>•", $array[0][$m][3]);
          }
          $str = strval($array[0][$m][2]);
          if((str_contains($str, 'CLO'))&&($array[0][$m][1]==null)&&($array[0][$m][3]!=null)&&($array[0][$m][15]==null)){
            if($CLO == ""){
              $CLO .= $array[0][$m][2].": ".$array[0][$m][3];
            }else{
              $CLO .= "<w:br/>".$array[0][$m][2].": ".$array[0][$m][3];
            }
          }
          if((str_contains($str, 'References'))&&($array[0][$m][1]!=null)&&($array[0][$m][8]!=null)){
            $references = str_replace("• Additional", "<w:br/>• Additional", $array[0][$m][8]);
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
      $course_table->addCell(10000,$styleCell)->addText($subject_code." : ".$course[0]->subject_name, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(500,$styleCell)->addText('2.',null, $noSpaceAndLeft);
      $course_table->addCell(2000,$styleCell)->addText('Year of Study (Programme): ', null, $noSpaceAndLeft);
      $course_table->addCell(10000,$styleCell)->addText('Year 1 and Year 2 ('.$programme_name.')' , null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(500,$styleCell)->addText('3.',null, $noSpaceAndLeft);
      $course_table->addCell(2000,$styleCell)->addText('Credit Hour: ', null, $noSpaceAndLeft);
      $course_table->addCell(10000,$styleCell)->addText($credit, null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(500,$styleCell)->addText('4.',null, $noSpaceAndLeft);
      $course_table->addCell(2000,$styleCell)->addText('Lecturer: ', null, $noSpaceAndLeft);
      $course_table->addCell(10000,$styleCell)->addText($name."( ".$staff_id." )", null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(500,$styleCell)->addText('5.',null, $noSpaceAndLeft);
      $course_table->addCell(2000,$styleCell)->addText('Tutor: ', null, $noSpaceAndLeft);
      $course_table->addCell(10000,$styleCell)->addText($name."( ".$staff_id." )", null, $noSpaceAndLeft);

      $course_table->addRow(1);
      $course_table->addCell(500,$styleCell)->addText('6.',null, $noSpaceAndLeft);
      $course_table->addCell(2000,$styleCell)->addText('Year and Trimester: ', null, $noSpaceAndLeft);
      $course_table->addCell(10000,$styleCell)->addText($semester_name, null, $noSpaceAndLeft);

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
            $table->addCell(2000)->addText('Y', array('bold' => true,'Color' => 'green'), $noSpaceAndCenter);
          }else{
            $table->addCell(2000)->addText('N', array('bold' => true,'Color' => 'red'), $noSpaceAndCenter);
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

      $num = 1;
      foreach($TP_CQI as $row){
        $table->addRow(5);
        $table->addCell(600,$styleCell)->addText($num,null, $noSpaceAndCenter);
        $table->addCell(6000,$styleCell)->addText($row->action, null, $noSpaceAndLeft);
        $table->addCell(6000,$styleCell)->addText($row->plan, null, $noSpaceAndLeft);
        $num++;
      }

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
            $t = 0;
            foreach($topic as $row_topic){
              $table->addRow(null);
              if($t==0){
                $table->addCell(600,$cellRowSpan)->addText($row->week,array( 'bold'=>true ), $noSpaceAndCenter);
              }else{
                $table->addCell(null,$cellRowContinue);
              }
          $L_topic = $table->addCell(5200);
          $lecture_topic = "";
          if($row_topic->lecture_topic!=""){
            $lecture_topic = explode('///',$row_topic->lecture_topic);
            \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$lecture_topic[1].'</b>',false);
          }else{
            \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,"<b>Topic: ".$row_topic->lecture_topic.'</b>',false);
          }
          $html = str_replace("<br>","<br/>",$row_topic->sub_topic);

          \PhpOffice\PhpWord\Shared\Html::addHtml($L_topic,$html,false);
          $table->addCell(800)->addText($row_topic->lecture_hour,null,$noSpaceAndCenter);
          if($t==0){
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
          $t++;
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
      $Moderator = DB::table('staffs')
                   ->join('users','staffs.user_id','=','users.user_id')
                   ->select('staffs.*','users.*')
                   ->where('staffs.id', '=', $course[0]->moderator)
                   ->get();

      $verified_by = DB::table('staffs')
                   ->join('users','staffs.user_id','=','users.user_id')
                   ->select('staffs.*','users.*')
                   ->where('staffs.id','=',$course[0]->verified_by)
                   ->get();

      $table->addRow(1);
      if($action[0]->prepared_date!=NULL){
        if($course[0]->staff_sign!=NULL){
          $s_p = storage_path('/private/staffSign/'.$course[0]->staff_sign);
          $table->addCell(4000)->addImage($s_p,array('width'=>80, 'height'=>40, 'align'=>'center'));
        }else{
          $table->addCell(4000,$styleCell)->addText($course[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
        }
      }else{
        $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
      }

      if($action[0]->verified_date!=NULL){
        if($Moderator[0]->staff_sign!=NULL){
          $s_m = storage_path('/private/staffSign/'.$Moderator[0]->staff_sign);
          $table->addCell(4000)->addImage($s_m,array('width'=>80, 'height'=>40, 'align'=>'center'));
        }else{
          $table->addCell(4000,$styleCell)->addText($Moderator[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
        }
      }else{
        $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
      }

      if($action[0]->approved_date!=NULL){
        if($verified_by[0]->staff_sign!=NULL){
          $s_v = storage_path('/private/staffSign/'.$verified_by[0]->staff_sign);
          $table->addCell(4000)->addImage($s_v,array('width'=>80, 'height'=>40, 'align'=>'center'));
        }else{
          $table->addCell(4000,$styleCell)->addText($verified_by[0]->name,array('bold' => true,'size' => 16),$noSpaceAndCenter);
        }
      }else{
        $table->addCell(4000,$styleCell)->addText("",Null,$noSpaceAndCenter);
      }

      $table->addRow(1);
      $table->addCell(4000)->addText('Prepared By : '.$course[0]->name.'<w:br/>Course Coordinator',null, $noSpaceAndLeft);
      $table->addCell(4000)->addText('Moderated By: '.$Moderator[0]->name.'<w:br/>Moderator', null, $noSpaceAndLeft);
      $table->addCell(4000)->addText('Approved By : '.$verified_by[0]->name.'<w:br/>'.$verified_by[0]->position, null, $noSpaceAndLeft);

      $table->addRow(1);
      $table->addCell(4000)->addText('Date: '.$action[0]->prepared_date,null, $noSpaceAndLeft);
      $table->addCell(4000)->addText('Date: '.$action[0]->verified_date,null, $noSpaceAndLeft);
      $table->addCell(4000)->addText('Date: '.$action[0]->approved_date,null, $noSpaceAndLeft);

    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
      $objWriter->save($semester_name." ".$subject_code." ".$subject_name.'.docx');
        $zip->addFile($semester_name." ".$subject_code." ".$subject_name.'.docx',$semester_name." ".$subject_code." ".$subject_name.'.docx');
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
                File::delete($semester_name." ".$subject_code." ".$subject_name.'.docx');
            }
        }
      return response()->download($fileName)->deleteFileAfterSend(true);
  }
}
