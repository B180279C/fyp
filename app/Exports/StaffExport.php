<?php

namespace App\Exports;

use App\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class StaffExport implements FromView
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
    	$staffs = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->join('departments', 'staffs.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'faculty.faculty_id', '=', 'departments.faculty_id')
                    ->select('staffs.*', 'users.email', 'users.name','users.position', 'departments.department_name','faculty.faculty_name')
                    ->orderBy('staffs.id')
                    ->get();
        return view('exports.Staff', [
            'staffs' => $staffs
        ]);
    }
}
