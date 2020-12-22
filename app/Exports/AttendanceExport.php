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

class AttendanceExport implements FromView
{
    use Exportable;
    protected $id;

    function __construct($id) {
        $this->id = $id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
        $user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->join('staffs', 'staffs.id','=','courses.lecturer')
                 ->join('users', 'staffs.user_id', '=' , 'users.user_id')
                 ->select('courses.*','subjects.*','semesters.*','users.*','staffs.*')
                 ->where('course_id', '=', $this->id)
                 ->get();

        $timetable = DB::table('timetable')
                    ->select('timetable.*')
                    ->where('timetable.course_id','=',$this->id)
                    ->where('timetable.status','=','Active')
                    ->orderBy('timetable.class_hour')
                    ->get();

        $attendance = DB::table('attendance')
                    ->join('timetable','timetable.tt_id','=','attendance.tt_id')
                    ->select('attendance.*','timetable.*')
                    ->where('timetable.course_id','=',$this->id)
                    ->orderBy('attendance.A_date')
                    ->orderBy('timetable.class_hour')
                    ->get();

        $assign_student = DB::table('assign_student_course')
                    ->join('students','students.student_id', '=', 'assign_student_course.student_id')
                    ->join('users','users.user_id', '=', 'students.user_id')
                    ->select('assign_student_course.*','students.*','users.*')
                    ->where('assign_student_course.course_id','=',$this->id)
                    ->where('assign_student_course.status','=',"Active")
                    ->orderBy('students.batch')
                    ->get();

        return view('exports.Attendance', [
            'course' => $course,
            'timetable' => $timetable,
            'attendance' => $attendance,
            'assign_student' => $assign_student,
        ]);
    }
}
