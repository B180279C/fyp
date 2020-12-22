<?php

namespace App\Exports;

use App\Faculty;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class DepartmentExport implements FromView
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
        $departments = DB::table('departments')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('departments.*', 'faculty.faculty_name')
                    ->get();
        return view('exports.Department', [
            'departments' => $departments
        ]);
    }
}
