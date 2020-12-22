<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\Department;
use App\Programme;
use App\Semester;
use App\Faculty;
use App\Exports\CourseExport;
use Maatwebsite\Excel\Facades\Excel;

class C_PortFolioController extends Controller
{
    public function index()
    {
        $user_id     = auth()->user()->user_id;
        $staff_dean  = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id  = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty     = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $department  = Department::where('department_id', '=', $department_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id = $last_semester->semester_id;

        if(auth()->user()->position=="Dean"){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();
        }else if(auth()->user()->position=="HoD"){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('departments.department_id', '=', $department_id)
                    ->where('courses.semester','=',$semester_id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();
        }
        return view('dean.CoursePortFolio.CoursePortFolio',compact('faculty','course','department'));
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
                        ->get();
            }else{
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
                        ->get();
            }
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if ($course->count()) {
                foreach($course as $row){
                    $result .= '<a href="'.$character.'/CourseList/action/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1">';
                    $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name">';
                    $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                    $result .= '</div>';
                    if(auth()->user()->position=="Dean"){
                        $result .= '<div class="col-1" id="course_action">';
                        $result .= '<i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                        $result .= '<i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                        $result .= '</div>';
                    }
                    $result .= '</div></a>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
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
                        ->where('courses.semester','=',$semester_id)
                        ->where('courses.status','=','Active')
                        ->orderBy('programmes.programme_id')
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
                        ->where('courses.semester','=',$semester_id)
                        ->where('courses.status','=','Active')
                        ->orderBy('programmes.programme_id')
                        ->get();
            }
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>';
            $result .= '</div>';
            foreach($course as $row){
                $result .= '<a href="'.$character.'/CourseList/action/'.$row->course_id.'" class="col-md-12 align-self-center" id="course_list">';
                $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1">';
                $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col" id="course_name">';
                $result .= '<p style="margin: 0px;"><b>'.$row->semester_name."</b> : ".$row->short_form_name." / ".$row->subject_code." ".$row->subject_name." ( ".$row->name.')</p>';
                $result .= '</div>';
                if(auth()->user()->position=="Dean"){
                    $result .= '<div class="col-1" id="course_action">';
                    $result .= '<i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                    $result .= '<i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_'.$row->course_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                    $result .= '</div>';
                }
                $result .= '</div></a>';
            }
        }
        return $result;
    }

    public function CourseListAction($id)
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;
        $department_id = $staff_dean->department_id;
        $faculty       = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id   = $last_semester->semester_id;
        if(auth()->user()->position=="Dean"){
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.course_id','=',$id)
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();
        }else{
            $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.course_id','=',$id)
                    ->where('departments.department_id', '=', $department_id)
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();
        }
        if(count($course)>0){
            return view('dean.CoursePortFolio.CourseListAction',compact('course','id'));
        }else{
            return redirect()->route('home');
        }
    }

    public function downloadExcel()
    {
        return Excel::download(new CourseExport, 'Courses.xlsx');
    }
}
