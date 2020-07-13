<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Staff;
use App\Academic;
use App\Programme;
use App\Subject;
use App\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dean = Staff::where('user_id', '=', Auth::User()->user_id)->firstOrFail();
        $programme = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('academic', 'departments.academic_id', '=', 'academic.academic_id')
                    ->select('programmes.*', 'academic.*')
                    ->where('academic.academic_id', '=', $dean->academic_id)
                    ->orderBy('programme_name')
                    ->get();
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.academic_id', '=', $dean->academic_id)
                    ->get();
        $lct = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->where('staffs.academic_id', '!=', $dean->academic_id)
                    ->get();
        $academic = Academic::all()->toArray();
        return view('dean.CourseCreate', compact('programme', 'staffs','lct','academic','dean'));
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
            if($staff->academic_id=="8"){
                $course_type = "MPU";
            }else{
                $course_type = "Normal";
            }
        $course = new Course([
            'subject_id'        => $request->get('subject'),
            'course_type'       => $course_type,
            'year'              => $request->get('year'),
            'semester'          => $request->get('semester'),
            'created_by'        => Auth::User()->user_id,
            'teacher'           => $request->get('lecturer'),
        ]);
        $course->save();
        return redirect()->back()->with('success','Data Added');
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
        //
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
        //
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
                    $data .= "<option value=$subject_id>$subject_code : $subject_name</option>";
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
}
