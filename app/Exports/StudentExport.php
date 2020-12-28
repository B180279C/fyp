<?php

namespace App\Exports;

use App\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class StudentExport implements FromView
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
    	$students = DB::table('students')
                    ->join('users', 'students.user_id', '=', 'users.user_id')
                    ->join('programmes','students.programme_id', '=', 'programmes.programme_id')
                    ->join('semesters','students.semester', '=', 'semesters.semester_id')
                    ->select('students.*', 'users.email', 'users.name', 'programmes.programme_name','programmes.short_form_name','semesters.*')
                    ->where('students.status_stu','=','Active')
                    ->get();
        return view('exports.Student', [
            'students' => $students
        ]);
    }
}
