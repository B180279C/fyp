<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Assign_Student_Course;
use App\Student;
use App\Staff;
use App\Faculty;
use App\Programme;
use App\Semester;
use App\Subject;
use App\Course;
use Excel;
use App\Imports\AssignStudentImport;

class AssignStudentController extends Controller
{
    public function viewAssignStudent($id){
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
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->where('faculty.faculty_id', '=', $faculty_id)
                    ->orderBy('programme_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        $assign_student = DB::table('assign_student_course')
        			->join('students','students.student_id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course[0]->course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();

        $batch = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course[0]->course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->groupBy('students.batch')
                    ->get();
        if(count($course)>0){
            return view('dean.AssignStudent.viewAssignStudent',compact('course','id','faculty','programme','semester','assign_student','batch'));
        }else{
            return redirect()->back();
        }
    }

    public function searchAssignStudent(Request $request)
    {
        $value = $request->get('value');
        $course_id = $request->get('course_id');
        $result = "";
        if($value!=""){
            $batch = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->Where(function($query) use ($value) {
                          $query->orWhere('students.student_id','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%')
                            ->orWhere('students.batch','LIKE','%'.$value.'%');
                    })
                    ->groupBy('students.batch')
                    ->get();
           $assign_student = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->Where(function($query) use ($value) {
                          $query->orWhere('students.student_id','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%')
                            ->orWhere('students.batch','LIKE','%'.$value.'%');
                    })
                    ->get();

            if(count($batch)>0){
                $i=0;
                foreach($batch as $row_batch){
                    $result .='<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                    $result .='<div class="col-12 row" style="padding:15px 10px 5px 10px;margin: 0px;">';
                    $result .='<h5 class="group plus" id="'.$i.'">'.$row_batch->batch.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                    $result .='</div>';
                    $result .='<div id="student_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px 0px 5px 0px;">';
                    foreach($assign_student as $row){
                        if($row->batch == $row_batch->batch){
                            $result .='<div class="col-md-4" style="margin: 0px;padding:2px;">';
                            $result .='<a href="" class="row" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;margin: 0px;">';
                            $result .='<div class="col-10" style="color: #0d2f81;padding: 10px;">';
                            $result .='<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>'.$row->name.' ( '.$row->student_id.')</b></p>';
                            $result .='</div>';
                            $result .='<div class="col-1" style="padding: 10px 20px;">';
                            $result .='<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->asc_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .='</div>';
                            $result .='</a>';
                            $result .='</div>';
                        }
                    }
                    $i++;
                    $result .='</div></div>';
                }
            }else{
                $result .= '<div class="col-md-12">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $batch = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->groupBy('students.batch')
                    ->get();
            $assign_student = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
            $i=0;
            foreach($batch as $row_batch){
                $result .='<div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">';
                $result .='<div class="col-12 row" style="padding:15px 10px 5px 10px;margin: 0px;">';
                $result .='<h5 class="group plus" id="'.$i.'">'.$row_batch->batch.' (<i class="fa fa-minus" aria-hidden="true" id="icon_'.$i.'" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>';
                $result .='</div>';
                $result .='<div id="student_'.$i.'" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px 0px 5px 0px;">';
                foreach($assign_student as $row){
                    if($row->batch == $row_batch->batch){
                        $result .='<div class="col-md-4" style="margin: 0px;padding:2px;">';
                        $result .='<a href="" class="row" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;margin: 0px;">';
                        $result .='<div class="col-10" style="color: #0d2f81;padding: 10px;">';
                        $result .='<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>'.$row->name.' ( '.$row->student_id.')</b></p>';
                        $result .='</div>';
                        $result .='<div class="col-1" style="padding: 10px 20px;">';
                        $result .='<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->asc_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                        $result .='</div>';
                        $result .='</a>';
                        $result .='</div>';
                    }
                }
                $i++;
                $result .='</div></div>';
            }
        }
        return $result;
    }

    public function showStudent(Request $request)
    {
        $programme = $request->get('programme');
        $programmes = Programme::where('programme_id', '=', $programme)->firstOrFail();
        $semester = $request->get('semester');
        $intake = $request->get('intake');
        $student = DB::table('students')
        		   ->join('users', 'users.user_id', '=', 'students.user_id')
        		   ->select('students.*','users.*')
        		   ->where('students.programme_id', '=', $programme)
        		   ->where('students.semester', '=', $semester)
        		   ->where('students.intake','=',$intake)
        		   ->orderBy('students.student_id')
        		   ->get();

        $data = "";
        foreach($student as $row){
            $id = $row->id;
            $student_id = $row->student_id;
            $name       = $row->name;
            $data .= "<option value=$id class='option'>$name ( $student_id )</option>";
        }

        if($data==""){
            return "null";
        }

        $result = "<optgroup label='$programmes->programme_name'>'".$data."'</optgroup>";
        return $result;
    }

    public function storeStudent(Request $request)
    {
    	$programme = $request->get('programme');
    	$semester = $request->get('semester');
        $intake = $request->get('intake');
        $student = $request->get('student');

		$failed = "";
        if($student!=""){
        	$checkexists = DB::table('assign_student_course')
                    ->select('assign_student_course.*')
                    ->where('assign_student_course.student_id','=',$student)
                    ->where('assign_student_course.status','=',"Active")
                    ->where('assign_student_course.course_id', '=', $request->get('course_id'))
                    ->get();
            if (count($checkexists) === 0) {
            	$assign_student_course = new Assign_Student_Course([
                'student_id'        => $student,
                'course_id'         => $request->get('course_id'),
                'status'            => "Active",
	            ]);
	            $assign_student_course->save();
	            return redirect()->back()->with('success','Data Added');
            }else{
            	return redirect()->back()->with('failed','The Student has been assigned in this course.');
            }
        }else{
        	$student_query = DB::table('students')
        		   ->join('users', 'users.user_id', '=', 'students.user_id')
        		   ->select('students.*','users.*')
        		   ->where('students.programme_id', '=', $programme)
        		   ->where('students.semester', '=', $semester)
        		   ->where('students.intake','=',$intake)
        		   ->orderBy('students.student_id')
        		   ->get();
        	foreach($student_query as $row){
        		$checkexists = DB::table('assign_student_course')
                    ->select('assign_student_course.*')
                    ->where('assign_student_course.student_id','=',$row->student_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->where('assign_student_course.course_id', '=', $request->get('course_id'))
                    ->get();
	            if (count($checkexists) === 0) {
	            	$assign_student_course = new Assign_Student_Course([
	                'student_id'        => $row->student_id,
	                'course_id'         => $request->get('course_id'),
	                'status'            => "Active",
		            ]);
		            $assign_student_course->save();
	            }else{
	            	$failed .= "The student ( ".$row->student_id." ) is already inserted.";
	            }
        	}
        }
        if($failed==""){
            return redirect()->back()->with('success','Data Added');
        }else{
            return redirect()->back()->with('failed', $failed);
        }
    }

    public function importExcelStudent(Request $request)
    {
        $array = (new AssignStudentImport)->toArray($request->file('file'));
        for($i=0;$i<=(count($array[0])-1);$i++){
            if(isset($array[0][$i]['student_id'])){
                $student_id = $array[0][$i]['student_id'];
                $student_name = $array[0][$i]['student_name'];
                if ($student_id === '' || $student_id === null) {
                    if($student_name === '' || $student_name === null){
                        $array[0][$i]['student_id'] = "Empty";
                        $array[0][$i]['student_name'] = "Empty";
                    }
                }
            }else{
                if($i==0){
                    return response()->json("Failed");
                }else{
                    $student_id = $array[0][$i]['student_id'];
                    $student_name = $array[0][$i]['student_name'];
                    if ($student_id === '' || $student_id === null) {
                        if($student_name === '' || $student_name === null){
                            $array[0][$i]['student_id'] = "Empty";
                            $array[0][$i]['student_name'] = "Empty";
                        }
                    }
                }
            }       
        }
        return response()->json($array[0]);
    }

    public function storeAssignStudent(Request $request)
    {
    	$count = $request->get('count');
        $failed = "";
        for($i=0;$i<=$count;$i++){
            $student_id   = $request->get('student_id'.$i);
            $student_name   = $request->get('student_name'.$i);

            for($c=0;$c<=$count;$c++){
                if(($i!=$c)&&($c>=$i)){
                    $c_student_id   = $request->get('student_id'.$c);
                    $c_student_name   = $request->get('student_name'.$c);
                    if($c_student_id==$student_id&&$c_student_name==$student_name){
                        $failed = "In No. ".($i+1)." , of student details is similar with No. ".($c+1).".<br/>";
                    }
                }
            }

            if(isset($student_id)!=""){
                $checkexists = DB::table('assign_student_course')
                    ->select('assign_student_course.*')
                    ->where('assign_student_course.student_id','=',$student_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->where('assign_student_course.course_id', '=', $request->get('course_id'))
                    ->get();
                if (count($checkexists) === 0) {

                }else{
                    $failed .= "The student ( ".$student_id." ) is already inserted.<br/>";
                }
            }else{
                $failed .= "The Student (".$student_id.") got something wrong.<br/>";
            }
        }
        if($failed==""){
            for($i=0;$i<=$count;$i++){
                $student_id   = $request->get('student_id'.$i);
                $student_name   = $request->get('student_name'.$i);
                $assign_student_course = new Assign_Student_Course([
                    'student_id'        => $student_id,
                    'course_id'         => $request->get('course_id'),
                    'status'            => "Active",
                ]);
                $assign_student_course->save();
            }
            return redirect()->back()->with('success','Data Added');
        }else{
            return redirect()->back()->with('failed', $failed);
        }
    }

    public function removeActiveStudent($id){
        $assign_student = Assign_Student_Course::where('asc_id', '=', $id)->firstOrFail();
        $assign_student->status  = "Remove";
        $assign_student->save();
        return redirect()->back()->with('success','Remove Successfully');
    }
}
