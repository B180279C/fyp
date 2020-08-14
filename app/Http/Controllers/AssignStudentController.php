<?php

namespace App\Http\Controllers;

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
                 ->select('courses.*','subjects.*')
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
        			->join('students','students.id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course[0]->course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
        if(count($course)>0){
            return view('dean.viewAssignStudent',compact('course','id','faculty','programme','semester','assign_student'));
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
            $assign_student = DB::table('assign_student_course')
        			->join('students','students.id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->Where(function($query) use ($value) {
                          $query->orWhere('students.student_id','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%');
                      })
                    ->get();
            if ($assign_student->count()) {
            	foreach($assign_student as $row){
                    $result .= '<div class="col-md-3 align-self-center" style="padding:0px 3px;">';
                    $result .= '<a href="" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;display: inline-block;width: 100%;">';
                    $result .= '<div class="col" style="padding: 10px;color: #0d2f81;">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>'.$row->name.' ( '.$row->student_id.' )</p>';
                    $result .= '</div></a></div>';
                }
            }else{
            	$result .= '<div class="col-md-12">';
                $result .= '<p>Not Found</p>';
                $result .= '</div>';
            }
        }else{
            $assign_student = DB::table('assign_student_course')
        			->join('students','students.id', '=', 'assign_student_course.student_id')
        			->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$course_id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
            foreach($assign_student as $row){
                    $result .= '<div class="col-md-3 align-self-center" style="padding:0px 3px;">';
                    $result .= '<a href="" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;display: inline-block;width: 100%;">';
                    $result .= '<div class="col" style="padding: 10px;color: #0d2f81;">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>'.$row->name.' ( '.$row->student_id.' )</p>';
                    $result .= '</div></a></div>';
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
                    ->where('assign_student_course.student_id','=',$row->id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
	            if (count($checkexists) === 0) {
	            	$assign_student_course = new Assign_Student_Course([
	                'student_id'        => $row->id,
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
            $student_id = $array[0][$i]['student_id'];
            $student_name = $array[0][$i]['student_name'];
            if ($student_id === '' || $student_id === null) {
                if($student_name === '' || $student_name === null){
                    $array[0][$i]['student_id'] = "Empty";
                    $array[0][$i]['student_name'] = "Empty";
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
            $students = DB::table('students')
            		->join('users','students.user_id','=','users.user_id')
                    ->select('students.*')
                    ->where('students.student_id', '=', $student_id)
                    ->where('users.name', '=', $student_name)
                    ->get();

            if(isset($students[0])){
               	$checkexists = DB::table('assign_student_course')
                    ->select('assign_student_course.*')
                    ->where('assign_student_course.student_id','=',$students[0]->id)
                    ->where('assign_student_course.status','=',"Active")
                    ->get();
                if (count($checkexists) === 0) {
                        $assign_student_course = new Assign_Student_Course([
		                'student_id'        => $students[0]->id,
		                'course_id'         => $request->get('course_id'),
		                'status'            => "Active",
			            ]);
			            $assign_student_course->save();
                }else{
                        $failed .= "The Student (".$student_id.") is already assigned.";
                }
            }else{
                $failed .= "The Student (".$student_id.") got something wrong.";
            }
        }
        if($failed==""){
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
