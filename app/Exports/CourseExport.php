<?php

namespace App\Exports;

use App\User;
use App\Course;
use App\Staff;
use App\Department;
use App\Programme;
use App\Semester;
use App\Faculty;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class CourseExport implements FromView
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
        $last_semester = DB::table('semesters')->orderBy('semester_name', 'desc')->first();
        $semester_id = $last_semester->semester_id;
        $staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*', 'users.*')
                    ->get();

        $timetable = DB::table('timetable')
                    ->join('courses', 'courses.course_id', '=', 'timetable.course_id')
                    ->select('timetable.*','courses.*')
                    ->where('timetable.status','=','Active')
                    ->get();

        $course = DB::table('courses')
                    ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                    ->join('programmes', 'programmes.programme_id', '=', 'subjects.programme_id')
                    ->join('departments', 'departments.department_id', '=', 'programmes.department_id')
                    ->join('semesters', 'semesters.semester_id', '=', 'courses.semester')
                    ->join('staffs', 'staffs.id','=','courses.lecturer')
                    ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                    ->select('courses.*','subjects.*','programmes.*','departments.*','semesters.*','staffs.*','users.*')
                    ->where('courses.status','=','Active')
                    ->orderBy('programmes.programme_id')
                    ->get();
        return view('exports.Courses', [
            'courses' => $course,
            'staffs' => $staffs,
            'timetable' => $timetable,
        ]);
    }
}
