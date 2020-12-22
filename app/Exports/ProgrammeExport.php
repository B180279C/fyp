<?php

namespace App\Exports;

use App\Programme;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ProgrammeExport implements FromView
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
        $programmes = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'departments.department_name', 'faculty.faculty_name')
                    ->orderBy('faculty.faculty_id')
                    ->get();
        return view('exports.Programme', [
            'programmes' => $programmes
        ]);
    }
}
