<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\Department;
use App\Programme;
use App\Semester;
use App\Faculty;
use App\Course;
use App\Timetable;
use App\Exports\CourseExport;
use App\Imports\CoursesImport;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{
	public function index()
	{
        $course = DB::table('courses')
                   ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                   ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                   ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                   ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                   ->join('staffs', 'staffs.id','=','courses.lecturer')
                   ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                   ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                   ->where('courses.status','=','Active')
                   ->orderByDesc('semesters.semester_name')
                   ->orderBy('programmes.programme_id')
                   ->get();

        return view('admin.Courses.CourseIndex',compact('course'));
	}

	public function create()
    {
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->orderBy('programme_name')
                    ->get();

        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->get();

        $moderator = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->get();

        $reviewer = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('users.status','=','Active')
                    ->where('users.position','!=','Teacher')
                    ->where('users.position','!=','Student')
                    ->where('users.position','!=','admin')
                    ->get();

        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();

        $faculty = Faculty::all()->toArray();
        return view('admin.Courses.CourseCreate', compact('programme','lct','faculty','semester','moderator','reviewer'));
    }

    public function courseSubject(Request $request)
    {
        $value = $request->get('value');

        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $value)
                    ->get();
        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $value)
                    ->groupBy('subject_type')
                    ->get();

        $data = "";
        $result = "";
        foreach($group as $row_group){
            $data .= "<optgroup label='$row_group->subject_type'>";
            foreach($subjects as $row){
                if($row_group->subject_type == $row->subject_type){
                    $subject_id   = $row->subject_id;
                    $subject_code = $row->subject_code;
                    $subject_name = $row->subject_name;
                    $data .= "<option value=$subject_id class='option-group'>$subject_code : $subject_name</option>";
                }
            }
            $data .= "</optgroup>";
        }

        if($data==""){
            return "null";
        }

        $result = $data;

        return $result;
    }

    public function changeModerator(Request $request)
    {
        $value = $request->get('value');

        $moderator = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->get();

        $faculty = Faculty::all()->toArray();

        $data = "";
        $result = "";
        foreach($faculty as $row_faculty){
            $faculty_name = $row_faculty['faculty_name'];
            $data .= "<optgroup label='$faculty_name'>";
            foreach($moderator as $row){
                if($row_faculty['faculty_id'] == $row->faculty_id){
                    if($value!=$row->id){
                        $data .= "<option value=$row->id class='option-group'>$row->name ( $row->staff_id )</option>";
                    }
                }
            }
            $data .= "</optgroup>";
        }

        if($data==""){
            return "null";
        }

        $result = $data;
        return $result;
    }

    public function store(Request $request)
    {
        $course_type = "Normal";
        $failed = "";
        $repeat_failed = "";
        $failed_data = "";
        $count = $request->get('count');
        for($i = 1;$i<=$count;$i++){
            $week = $request->get('week'.$i);
            $s_hour = $request->get('s_hour'.$i);
            $e_hour = $request->get('e_hour'.$i);
            $last_hour = $this->getFullTime($s_hour,$e_hour);
            $sperate = explode(',',$last_hour);
            for($s=0;$s<=count($sperate)-1;$s++){
                for($m = 1;$m<=$count;$m++){
                    if(($i!=$m)&&($m>=$i)){
                        $m_week = $request->get('week'.$m);
                        if($week==$m_week){
                            $m_s_hour = $request->get('s_hour'.$m);
                            $m_e_hour = $request->get('e_hour'.$m);
                            $m_last_hour = $this->getFullTime($m_s_hour,$m_e_hour);
                            $m_sperate = explode(',',$m_last_hour);
                            $now = $sperate[$s];
                            for($x=0;$x<=count($m_sperate)-1;$x++){ 
                                if($now==$m_sperate[$x]){
                                    $repeat_failed .= $m_sperate[$x].',';
                                }
                            }
                        }
                    }
                }
            }
        }

        $checkTimetable = DB::table('timetable')
                    ->join('courses','courses.course_id','=','timetable.course_id')
                    ->select('timetable.*','courses.*')
                    ->where('courses.semester','=',$request->get('semester'))
                    ->where('courses.lecturer','=',$request->get('lecturer'))
                    ->where('courses.status','=',"Active")
                    ->where('timetable.status','=',"Active")
                    ->get();

        if(count($checkTimetable)>0){
            foreach($checkTimetable as $row){
                $data_week = $row->week;
                $class_hour = $row->class_hour;
                $data_sperate = explode(',',$class_hour);
                for($s=0;$s<=count($data_sperate)-1;$s++){
                    for($i = 1;$i<=$count;$i++){
                        $week = $request->get('week'.$i);
                        if($data_week==$week){
                            $s_hour = $request->get('s_hour'.$i);
                            $e_hour = $request->get('e_hour'.$i);
                            $last_hour = $this->getFullTime($s_hour,$e_hour);
                            $sperate = explode(',',$last_hour);
                            $now = $data_sperate[$s];
                            for($x=0;$x<=count($sperate)-1;$x++){ 
                                if($now==$sperate[$x]){
                                    $failed .= $sperate[$x].',';
                                }
                            }
                        }
                    }
                }
            }
        }

        if($failed!=""){
            $failed_data .= "The lecturer of time (".$failed.") get class already.<br/>";
        }
        if($repeat_failed!=""){
            $failed_data .= "The timetable of class get repeated already (".$repeat_failed.").";
        }

        $checkexists = DB::table('courses')
                    ->select('courses.*')
                    ->where('courses.subject_id','=',$request->get('subject'))
                    ->where('courses.semester','=',$request->get('semester'))
                    ->where('courses.lecturer','=',$request->get('lecturer'))
                    ->where('courses.status','=',"Active")
                    ->get();
        if($failed_data==""){
            if (count($checkexists) === 0){
                $course = new Course([
                    'subject_id'        => $request->get('subject'),
                    'course_type'       => $course_type,
                    'semester'          => $request->get('semester'),
                    'lecturer'          => $request->get('lecturer'),
                    'moderator'         => $request->get('moderator'),
                    'verified_by'       => $request->get('verified_by'),
                    'approved_by'       => $request->get('approved_by'),
                    'credit'            => $request->get('credit'),
                    'status'            => "Active",
                ]);
                $course->save();
                $course_id = $course->course_id;
                for($i = 1;$i<=$count;$i++){
                    $week = $request->get('week'.$i);
                    if($week!=""){
                        $s_hour = $request->get('s_hour'.$i);
                        $e_hour = $request->get('e_hour'.$i);
                        $last_hour = $this->getFullTime($s_hour,$e_hour);
                        $f_or_h = $request->get('hORf'.$i);
                        $timetable = new Timetable([
                            'course_id'         => $course_id,
                            'week'              => $week,
                            'class_hour'        => $last_hour,
                            'F_or_H'            => $f_or_h,
                            'status'            => "Active",
                        ]);
                        $timetable->save();
                    }
                }
                return redirect()->back()->with('success','Data Added');
            }else{
                return redirect()->back()->with('failed','The Subject has been existed');
            }
        }else{
            return redirect()->back()->with('failed',$failed_data);
        }
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

    public function edit($id){
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->orderBy('programme_name')
                    ->get();
        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->get();

        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();

        $faculty = Faculty::all()->toArray();

        $course  = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->select('courses.*', 'subjects.*')
                    ->where('courses.course_id', '=', $id)
                    ->get();

        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $course[0]->programme_id)
                    ->get();

        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $course[0]->programme_id)
                    ->groupBy('subject_type')
                    ->get();

        $staff_check_inFaculty = DB::table('staffs')
                    ->select('staffs.*')
                    ->where('staffs.id', '=', $course[0]->lecturer)
                    ->get();

        $count_staff_InFaculty = count($staff_check_inFaculty);

        $moderator = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->get();

        $reviewer = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('users.status','=','Active')
                    ->where('users.position','!=','Teacher')
                    ->where('users.position','!=','Student')
                    ->where('users.position','!=','admin')
                    ->get();

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$id)
                    ->where('timetable.status','=','Active')
                    ->get();

        return view('admin.Courses.CourseEdit', compact('programme','lct','faculty','semester','group','subjects','course','count_staff_InFaculty','moderator', 'id','reviewer','timetable'));
    }

    public function update(Request $request, $id)
    {
        $failed = "";
        $repeat_failed = "";
        $failed_data = "";
        $count = $request->get('count');
        for($i = 1;$i<=$count;$i++){
            $week   = $request->get('week'.$i);
            if($week!=""){
                $s_hour = $request->get('s_hour'.$i);
                $e_hour = $request->get('e_hour'.$i);
                $last_hour = $this->getFullTime($s_hour,$e_hour);
                $sperate = explode(',',$last_hour);
                for($s=0;$s<=count($sperate)-1;$s++){
                    for($m = 1;$m<=$count;$m++){
                        if(($i!=$m)&&($m>=$i)){
                            $m_week = $request->get('week'.$m);
                            if($week==$m_week){
                                $m_s_hour = $request->get('s_hour'.$m);
                                $m_e_hour = $request->get('e_hour'.$m);
                                $m_last_hour = $this->getFullTime($m_s_hour,$m_e_hour);
                                $m_sperate = explode(',',$m_last_hour);
                                $now = $sperate[$s];
                                for($x=0;$x<=count($m_sperate)-1;$x++){ 
                                    if($now==$m_sperate[$x]){
                                        $repeat_failed .= $m_week.":".$m_sperate[$x].',';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $checkTimetable = DB::table('timetable')
                    ->join('courses','courses.course_id','=','timetable.course_id')
                    ->select('timetable.*','courses.*')
                    ->where('courses.semester','=',$request->get('semester'))
                    ->where('courses.lecturer','=',$request->get('lecturer'))
                    ->where('timetable.course_id','!=',$id)
                    ->where('courses.status','=',"Active")
                    ->where('timetable.status','=',"Active")
                    ->get();

        if(count($checkTimetable)>0){
            foreach($checkTimetable as $row){
                $data_week = $row->week;
                $class_hour = $row->class_hour;
                $data_sperate = explode(',',$class_hour);
                for($s=0;$s<=count($data_sperate)-1;$s++){
                    for($i = 1;$i<=$count;$i++){
                        $week = $request->get('week'.$i);
                        if($week!=""){
                            if($data_week==$week){
                                $s_hour = $request->get('s_hour'.$i);
                                $e_hour = $request->get('e_hour'.$i);
                                $last_hour = $this->getFullTime($s_hour,$e_hour);
                                $sperate = explode(',',$last_hour);
                                $now = $data_sperate[$s];
                                for($x=0;$x<=count($sperate)-1;$x++){ 
                                    if($now==$sperate[$x]){
                                        $failed .= $week.":".$sperate[$x].',';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if($failed!=""){
            $failed_data .= "The lecturer of time (".$failed.") get class already.<br/>";
        }
        if($repeat_failed!=""){
            $failed_data .= "The timetable of class get repeated already (".$repeat_failed.").";
        }

        if($failed_data==""){
            $course = Course::where('course_id', '=', $id)->firstOrFail();
            if($course->subject_id!=$request->get('subject')){
                $checkexists = DB::table('courses')
                            ->select('courses.*')
                            ->where('courses.subject_id','=',$request->get('subject'))
                            ->where('courses.semester','=',$request->get('semester'))
                            ->where('courses.status','=',"Active")
                            ->get();
                if (count($checkexists) === 0) {
                    $course->subject_id = $request->get('subject');
                }else{
                    return redirect()->back()->with('failed','The Subject has been existed');
                }
            }
            $course->semester      = $request->get('semester');
            $course->credit        = $request->get('credit');
            $course->lecturer      = $request->get('lecturer');
            $course->moderator     = $request->get('moderator');
            $course->verified_by   = $request->get('verified_by');
            $course->approved_by   = $request->get('approved_by');
            $course->save();

            for($i = 1;$i<=$count;$i++){
                $tt_id  = $request->get('tt_id'.$i);
                $week   = $request->get('week'.$i);
                if($week!=""){
                    $s_hour = $request->get('s_hour'.$i);
                    $e_hour = $request->get('e_hour'.$i);
                    $last_hour = $this->getFullTime($s_hour,$e_hour);
                    $f_or_h = $request->get('hORf'.$i);
                    if($tt_id!=0){
                        echo $tt_id;
                        $timetable = Timetable::where('tt_id', '=', $tt_id)->firstOrFail();
                        $timetable->week = $week;
                        $timetable->class_hour = $last_hour;
                        $timetable->F_or_H = $f_or_h;
                        $timetable->save();
                    }else{
                        $timetable = new Timetable([
                            'course_id'         => $id,
                            'week'              => $week,
                            'class_hour'        => $last_hour,
                            'F_or_H'            => $f_or_h,
                            'status'            => "Active",
                        ]);
                        $timetable->save();
                    } 
                }
            }
            return redirect()->back()->with('success','Data Updated');
        }else{
            return redirect()->back()->with('failed',$failed_data);
        }
    }

    public function removeActiveTimetable($id){
        $timetable = Timetable::where('tt_id', '=', $id)->firstOrFail();
        $timetable->status = "Remove";
        $timetable->save();
        return redirect()->back()->with('success','Timetable Remove Successfully');
    } 

    public function removeActiveCourse($id){
        $course = Course::where('course_id', '=', $id)->firstOrFail();
        $course->status  = "Remove";
        $course->save();
        return redirect()->back()->with('success','Remove Successfully');
    }


    public function importExcel(Request $request)
    {
        $array = (new CoursesImport)->toArray($request->file('file'));
        for($i=0;$i<=(count($array[0])-1);$i++){
            if(isset($array[0][$i]['programme'])){
                $value = $array[0][$i]['subject_code'];
                $programme = $array[0][$i]['programme'];
                if ($value === '' || $value === null) {
                    if($programme === '' || $programme === null){
                            $array[0][$i]['programme'] = "Empty";
                            $array[0][$i]['subject_code'] = "Empty";
                    }
                }
            }else{
                if($i==0){
                    return response()->json("Failed");
                }else{
                    $value = $array[0][$i]['subject_code'];
                    $programme = $array[0][$i]['programme'];
                    if ($value === '' || $value === null) {
                        if($programme === '' || $programme === null){
                                $array[0][$i]['programme'] = "Empty";
                                $array[0][$i]['subject_code'] = "Empty";
                        }
                    }
                }
            }
        }
        return response()->json($array[0]);
    }

    public function storeCourses(Request $request)
    {
        $course_type = "Normal";
        $count = $request->get('count');
        $failed = "";
        $failed_data = "";
        $repeat_line_failed = "";
        $repeat_newLine_failed = "";
        $repeat_data_failed = "";
        for($i=0;$i<=$count;$i++){
            // echo "<br/>".$i."<br/>";
            $subject_code   = $request->get('subject_code'.$i);
            $subject_name   = $request->get('subject_name'.$i);
            $semester_name  = $request->get('semester'.$i);
            $credit         = $request->get('credit'.$i);
            $lecturer       = $request->get('lecturer'.$i);
            $moderator      = $request->get('moderator'.$i);
            $verified_by    = $request->get('verified_by'.$i);
            $approved_by    = $request->get('approved_by'.$i);
            $programme_name = $request->get('programme'.$i);
            $timetable      = $request->get('timetable'.$i);
            $programme      = Programme::where('programme_name', '=', $programme_name)->first();

            $subject = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('subjects.subject_code', '=', $subject_code)
                    ->where('subjects.programme_id', '=', $programme->programme_id)
                    ->get();

            $semester = Semester::where('semester_name', '=', $semester_name)->first();

            $firstStaff_id = explode("(",$lecturer);
            $secondStaff_id = explode(")",$firstStaff_id[1]);

            for($c=0;$c<=$count;$c++){
                if(($i!=$c)&&($c>=$i)){
                    $c_subject_code   = $request->get('subject_code'.$c);
                    $c_subject_name   = $request->get('subject_name'.$c);
                    $c_lecturer  = $request->get('lecturer'.$c);
                    if($c_lecturer==$lecturer&&$c_subject_code==$subject_code&&$c_subject_name==$subject_name){
                        $failed = "In No. ".($i+1)." , of subject details is similar with No. ".($c+1).".<br/>";
                    }
                }
            }

            $moderatorStaff_id = explode("(",$moderator);
            $second_moderatorStaff_id = explode(")",$moderatorStaff_id[1]);

            $verifiedStaff_id = explode("(",$verified_by);
            $second_verifiedStaff_id = explode(")",$verifiedStaff_id[1]);

            $approvedStaff_id = explode("(",$approved_by);
            $second_approvedStaff_id = explode(")",$approvedStaff_id[1]);

            $staff = Staff::where('staff_id', "=", $secondStaff_id[0])->first();

            $moderator_staff = Staff::where('staff_id', "=", $second_moderatorStaff_id[0])->first();

            $verified_staff = DB::table('staffs')
                    ->join('users','users.user_id','=','staffs.user_id')
                    ->select('staffs.*','users.*')
                    ->where('staffs.staff_id', '=', $second_verifiedStaff_id[0])
                    ->where('users.position', '=', 'HoD')
                    ->get();

            $approved_staff = DB::table('staffs')
                    ->join('users','users.user_id','=','staffs.user_id')
                    ->select('staffs.*','users.*')
                    ->where('staffs.staff_id', '=', $second_approvedStaff_id[0])
                    ->where('users.position', '=', 'Dean')
                    ->get();

            if(isset($subject[0])){
                if(($semester === null)||($staff === null)||($moderator_staff === null)){
                   $failed .= "In No. ".($i+1)." , the semester or staff(Lecturer,moderator) got something wrong.<br/>";
                }else if(count($verified_staff)==0||count($approved_staff)==0){
                    $failed .= "In No. ".($i+1)." ,The Verified By (Staff ID) must be HoD and the Approved By (Staff ID) must be Dean.<br/>";
                }else if($secondStaff_id[0]==$second_moderatorStaff_id[0]){
                    $failed .= "In No. ".($i+1)." , the Lecturer and Moderator cannot be same.<br/>";
                }else{
                    $checkexists = DB::table('courses')
                            ->select('courses.*')
                            ->where('courses.subject_id','=', $subject[0]->subject_id)
                            ->where('courses.semester','=', $semester->semester_id)
                            ->where('courses.lecturer','=', $staff->id)
                            ->where('courses.status','=',"Active")
                            ->get();
                    $repeat_failed = "";
                    $repeat_failed_2 = "";
                    if (count($checkexists) === 0) {
                        $classList = explode(';',$timetable);
                        for($t=0;$t<(count($classList)-1);$t++){
                            $timelist = explode(',',$classList[$t]);
                            $week = str_replace(' ', '', $timelist[0]);
                            $time = $timelist[1];
                            $hour = explode('-',$time);
                            $s_hour = intval($hour[0]);
                            $e_hour = intval($hour[1]);
                            $last_hour = $this->getFullTime($s_hour,$e_hour);
                            $sperate = explode(',',$last_hour);
                            for($s=0;$s<=count($sperate)-1;$s++){
                                $now = $sperate[$s];
                                for($m = 0;$m<(count($classList)-1);$m++){
                                    if(($t!=$m)&&($m>=$t)){
                                        $m_timelist = explode(',',$classList[$m]);
                                        $m_week = str_replace(' ', '', $m_timelist[0]);
                                        $m_time = $m_timelist[1];
                                        if($week==$m_week){
                                            $m_hour = explode('-',$m_time);
                                            $m_s_hour = intval($m_hour[0]);
                                            $m_e_hour = intval($m_hour[1]);
                                            $m_last_hour = $this->getFullTime($m_s_hour,$m_e_hour);
                                            $m_sperate = explode(',',$m_last_hour);
                                            
                                            for($x=0;$x<=count($m_sperate)-1;$x++){ 
                                                if($now==$m_sperate[$x]){
                                                    $repeat_failed .= $m_week.":".$m_sperate[$x].',';
                                                }
                                            }
                                        }
                                    }
                                }

                                for($num=0;$num<=$count;$num++){
                                    if(($i!=$num)&&($num>=$i)){
                                        $new_line_lecturer = $request->get('lecturer'.$num);
                                        if($lecturer==$new_line_lecturer){
                                            // echo $num."  ".$last_hour."<br/>";
                                            $new_line_timetable = $request->get('timetable'.$num);
                                            $new_line_classList = explode(';',$new_line_timetable);
                                            for($k=0;$k<(count($new_line_classList)-1);$k++){
                                                $new_line_timelist = explode(',',$new_line_classList[$k]);
                                                $new_line_week = str_replace(' ', '', $new_line_timelist[0]);
                                                if($week==$new_line_week){
                                                    $new_line_time = $new_line_timelist[1];
                                                    $new_line_hour = explode('-',$new_line_time);
                                                    $new_line_s_hour = intval($new_line_hour[0]);
                                                    $new_line_e_hour = intval($new_line_hour[1]);
                                                    $new_line_last_hour = $this->getFullTime($new_line_s_hour,$new_line_e_hour);
                                                    $new_line_sperate = explode(',',$new_line_last_hour);
                                                    for($n=0;$n<=count($new_line_sperate)-1;$n++){
                                                        if($now==$new_line_sperate[$n]){
                                                            $repeat_failed_2 = "No. ".($num+1)." ".$new_line_week.",".$new_line_sperate[$n];
                                                            $repeat_newLine_failed .= "In No. ".($i+1).", of timetable get repeated with ".$repeat_failed_2.".<br/>";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                               $checkTimetable = DB::table('timetable')
                                        ->join('courses','courses.course_id','=','timetable.course_id')
                                        ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                                        ->select('timetable.*','courses.*','subjects.*')
                                        ->where('courses.semester','=',$semester->semester_id)
                                        ->where('courses.lecturer','=',$staff->id)
                                        ->where('timetable.week','=',$week)
                                        ->where('timetable.class_hour','LIKE',"%".$now."%")
                                        ->where('courses.status','=',"Active")
                                        ->where('timetable.status','=',"Active")
                                        ->get();
                                if(count($checkTimetable)>0){
                                    $repeat_data_failed .= "In No. ".($i+1).", the timeatable get repeated with ".$checkTimetable[0]->subject_code." in ".$week."  ".$now."<br/>";
                                }
                            }
                        }
                        if($repeat_failed!=""){
                            $repeat_line_failed .= "In No.".($i+1).", the timetable got error. In ( ".$repeat_failed." ) are repeated.<br/>";
                        }
                        
                    }else{
                        $failed .= "In No.".($i+1)." , The course is already inserted.<br/>";
                    }
                }
            }else{
                $failed .= "In No.".($i+1)." , The subject details got something wrong.<br/>";
            }
        }
        if($failed!=""||$repeat_line_failed!=""||$repeat_newLine_failed!=""||$repeat_data_failed!=""){
            return redirect()->back()->with('failed', $failed.$repeat_line_failed.$repeat_newLine_failed.$repeat_data_failed);
        }else{
            for($i=0;$i<=$count;$i++){
                $subject_code   = $request->get('subject_code'.$i);
                $subject_name   = $request->get('subject_name'.$i);
                $semester_name  = $request->get('semester'.$i);
                $credit         = $request->get('credit'.$i);
                $lecturer       = $request->get('lecturer'.$i);
                $moderator      = $request->get('moderator'.$i);
                $verified_by    = $request->get('verified_by'.$i);
                $approved_by    = $request->get('approved_by'.$i);
                $programme_name = $request->get('programme'.$i);
                $timetable      = $request->get('timetable'.$i);
                $programme      = Programme::where('programme_name', '=', $programme_name)->first();

                $subject = DB::table('subjects')
                        ->select('subjects.*')
                        ->where('subjects.subject_code', '=', $subject_code)
                        ->where('subjects.programme_id', '=', $programme->programme_id)
                        ->get();
                $semester = Semester::where('semester_name', '=', $semester_name)->first();

                $firstStaff_id = explode("(",$lecturer);
                $secondStaff_id = explode(")",$firstStaff_id[1]);

                $moderatorStaff_id = explode("(",$moderator);
                $second_moderatorStaff_id = explode(")",$moderatorStaff_id[1]);

                $verifiedStaff_id = explode("(",$verified_by);
                $second_verifiedStaff_id = explode(")",$verifiedStaff_id[1]);

                $approvedStaff_id = explode("(",$approved_by);
                $second_approvedStaff_id = explode(")",$approvedStaff_id[1]);

                $staff = Staff::where('staff_id', "=", $secondStaff_id[0])->first();

                $moderator_staff = Staff::where('staff_id', "=", $second_moderatorStaff_id[0])->first();

                $verified_staff = DB::table('staffs')
                        ->join('users','users.user_id','=','staffs.user_id')
                        ->select('staffs.*','users.*')
                        ->where('staffs.staff_id', '=', $second_verifiedStaff_id[0])
                        ->where('users.position', '=', 'HoD')
                        ->get();

                $approved_staff = DB::table('staffs')
                        ->join('users','users.user_id','=','staffs.user_id')
                        ->select('staffs.*','users.*')
                        ->where('staffs.staff_id', '=', $second_approvedStaff_id[0])
                        ->where('users.position', '=', 'Dean')
                        ->get();

                $course = new Course([
                    'subject_id'        => $subject[0]->subject_id,
                    'course_type'       => $course_type,
                    'semester'          => $semester->semester_id,
                    'credit'            => $credit,
                    'lecturer'          => $staff->id,
                    'moderator'         => $moderator_staff->id,
                    'verified_by'       => $verified_staff[0]->id,
                    'approved_by'       => $approved_staff[0]->id,
                    'status'            => "Active",
                ]);
                $course->save();
                $course_id = $course->course_id;
                $classList = explode(';',$timetable);
                for($t=0;$t<(count($classList)-1);$t++){
                    $timelist = explode(',',$classList[$t]);
                    $week = str_replace(' ', '', $timelist[0]);
                    $time = $timelist[1];
                    $hour = explode('-',$time);
                    $s_hour = intval($hour[0]);
                    $e_hour = intval($hour[1]);
                    $f_or_h = $timelist[2];
                    $last_hour = $this->getFullTime($s_hour,$e_hour);
                    $insertTT = new Timetable([
                        'course_id'         => $course_id,
                        'week'              => $week,
                        'class_hour'        => $last_hour,
                        'F_or_H'            => $f_or_h,
                        'status'            => "Active",
                    ]);
                    $insertTT->save();
                }
                return redirect()->back()->with('success','Data Added');
            }
        }
    }
}