<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Staff;
use App\Faculty;
use App\Programme;
use App\Semester;
use App\Subject;
use App\Course;
use Excel;
use App\Imports\CoursesImport;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_id', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->orderBy('courses.subject_id')
                    ->get();
        return view('dean.CourseIndex',compact('course'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_id      = auth()->user()->user_id;
        $staff_dean   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id   = $staff_dean->faculty_id;
        $faculty_name = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->where('faculty.faculty_id', '=', $faculty_id)
                    ->orderBy('programme_name')
                    ->get();
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->get();
        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '!=', $faculty_id)
                    ->get();
        $semester = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.semester_name')
                    ->get();
        $faculty = Faculty::all()->toArray();
        return view('dean.CourseCreate', compact('programme', 'staffs','lct','faculty','faculty_name','semester'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $staff = Staff::where('user_id', '=', Auth::User()->user_id)->firstOrFail();
            if($staff->faculty_id=="8"){
                $course_type = "MPU";
            }else{
                $course_type = "Normal";
            }
        $checkexists = DB::table('courses')
                    ->select('courses.*')
                    ->where('courses.subject_id','=',$request->get('subject'))
                    ->where('courses.semester','=',$request->get('semester'))
                    ->where('courses.lecturer','=',$request->get('lecturer'))
                    ->where('courses.status','=',"Active")
                    ->get();

        if (count($checkexists) === 0) {
            $course = new Course([
                'subject_id'        => $request->get('subject'),
                'course_type'       => $course_type,
                'semester'          => $request->get('semester'),
                'lecturer'          => $request->get('lecturer'),
                'status'            => "Active",
            ]);
            $course->save();
            return redirect()->route('dean.C_potrfolio.index')->with('success','Data Added');
        }else{
            return redirect()->back()->with('failed','The Subject has been existed');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user_id      = auth()->user()->user_id;
        $staff_dean   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id   = $staff_dean->faculty_id;
        $faculty_name = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'faculty.*')
                    ->where('faculty.faculty_id', '=', $faculty_id)
                    ->orderBy('programme_name')
                    ->get();
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->get();
        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.faculty_id', '!=', $faculty_id)
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
                    ->where('staffs.faculty_id','=', $faculty_id)
                    ->get();
        $count_staff_InFaculty = count($staff_check_inFaculty);
        return view('dean.CourseEdit', compact('programme', 'staffs','lct','faculty','faculty_name','semester','group','subjects','course','count_staff_InFaculty', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course                = Course::where('course_id', '=', $id)->firstOrFail();

        if($course->subject_id!=$request->get('subject')){
            $checkexists = DB::table('courses')
                        ->select('courses.*')
                        ->where('courses.subject_id','=',$request->get('subject'))
                        ->where('courses.semester','=',$request->get('semester'))
                        ->where('courses.status','=',"Active")
                        ->get();
            if (count($checkexists) === 0) {
                $course->subject_id    = $request->get('subject');
            }else{
                return redirect()->back()->with('failed','The Subject has been existed');
            }
        }
        $course->semester      = $request->get('semester');
        $course->lecturer      = $request->get('lecturer');
        $course->save();
        return redirect()->route('dean.C_potrfolio.index')->with('success','Data Updated');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
            $value = $array[0][$i]['subject_code'];
            $programme = $array[0][$i]['programme'];
            if ($value === '' || $value === null) {
                if($programme === '' || $programme === null){
                    $array[0][$i]['programme'] = "Empty";
                    $array[0][$i]['subject_code'] = "Empty";
                }
            }
        }
        return response()->json($array[0]);
    }

    public function storeCourses(Request $request)
    {
        $staff = Staff::where('user_id', '=', Auth::User()->user_id)->firstOrFail();
            if($staff->faculty_id=="8"){
                $course_type = "MPU";
            }else{
                $course_type = "Normal";
            }
        $count = $request->get('count');
        $failed = "";
        for($i=0;$i<=$count;$i++){
            $subject_code   = $request->get('subject_code'.$i);
            $subject_name   = $request->get('subject_name'.$i);
            $semester_name  = $request->get('semester'.$i);
            $lecturer       = $request->get('lecturer'.$i);
            $programme_name = $request->get('programme'.$i);
            $programme = Programme::where('programme_name', '=', $programme_name)->first();

            $subject = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('subjects.subject_code', '=', $subject_code)
                    ->where('subjects.programme_id', '=', $programme->programme_id)
                    ->get();
            $semester = Semester::where('semester_name', '=', $semester_name)->first();

            $firstStaff_id = explode("(",$lecturer);
            $secondStaff_id = explode(")",$firstStaff_id[1]);

            $staff = Staff::where('staff_id', "=", $secondStaff_id[0])->first();

            if(isset($subject[0])){
                if(($semester === null)||($staff === null)){
                   $failed .= "In No ".($i+1)." , the semester or staff got something wrong.";
                }else{
                    $checkexists = DB::table('courses')
                            ->select('courses.*')
                            ->where('courses.subject_id','=', $subject[0]->subject_id)
                            ->where('courses.semester','=', $semester->semester_id)
                            ->where('courses.lecturer','=', $staff->id)
                            ->where('courses.status','=',"Active")
                            ->get();
                    if (count($checkexists) === 0) {
                        $course = new Course([
                            'subject_id'        => $subject[0]->subject_id,
                            'course_type'       => $course_type,
                            'semester'          => $semester->semester_id,
                            'lecturer'          => $staff->id,
                            'status'            => "Active",
                        ]);
                        $course->save();
                    }else{
                        $failed .= "In No ".($i+1)." , The course is already inserted.";
                    }
                }
            }else{
                $failed .= "In No ".($i+1)." , The subject details got something wrong.";
            }
        }
        if($failed==""){
            return redirect()->route('dean.C_potrfolio.index')->with('success','Data Added');
        }else{
            return redirect()->route('dean.C_potrfolio.index')->with('failed', $failed);
        }
    }

    public function searchTeachCourse(Request $request)
    {
        $user_id     = auth()->user()->user_id;
        $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id  = $staff_dean->faculty_id;
        $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_id', 'desc')->first();
        $semester_id = $last_semester->semester_id;

        $value = $request->get('value');

        $result = "";
        if($value!=""){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.status','=','Active')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('semesters.semester_name','LIKE','%'.$value.'%');
                      })
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            $result .= '<p id="marking">
                            <span style="padding:0px 10px;">Plan</span>
                            <span style="padding:0px 10px;">Note</span>
                            <span style="padding:0px 10px;">Assessment</span>
                        </p>';
            if ($course->count()) {
                foreach($course as $row){
                    $result .= '<a href="" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1">';
                    $result .= '<img src="'.url("image/subject.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                    $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->subject_code." ".$row->subject_name.'</p>';
                    $result .= '<p id="mark_data">
                                  <i class="fa fa-check correct" aria-hidden="true"></i>
                                  <i class="fa fa-check correct" aria-hidden="true"></i>
                                  <i class="fa fa-times wrong" aria-hidden="true" style="width: 90px"></i>
                              </p>';
                    $result .= '</div></div></a>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.lecturer', '=', $staff_dean->id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Newest Semester of Courses</p>';
            $result .= '<p id="marking">
                            <span style="padding:0px 10px;">Plan</span>
                            <span style="padding:0px 10px;">Note</span>
                            <span style="padding:0px 10px;">Assessment</span>
                        </p>';
            $result .= '</div>';
            foreach($course as $row){
                $result .= '<a href="" class="col-md-12 align-self-center" id="course_list">';
                $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1">';
                $result .= '<img src="'.url("image/subject.png").'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col" id="course_name" style="padding-top: 2px;">';
                $result .= '<p style="margin: 0px;display: inline-block;"><b>'.$row->semester_name."</b> : ".$row->subject_code." ".$row->subject_name.'</p>';
                $result .= '<p id="mark_data">
                                <i class="fa fa-check correct" aria-hidden="true"></i>
                                <i class="fa fa-check correct" aria-hidden="true"></i>
                                <i class="fa fa-times wrong" aria-hidden="true" style="width: 90px"></i>
                            </p>';
                $result .= '</div></div></a>';
            }
        }
        return $result;
    }


    public function courseAction($id){
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->select('courses.*','subjects.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();
        if(count($course)>0){
            return view('dean.CourseAction',compact('course','id'));
        }else{
            return redirect()->back();
        }
    }
}
