<?php

namespace App\Exports;

use App\Subject_MPU;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class MPUSubjectExport implements FromView
{
    use Exportable;
    protected $level;

    function __construct($level) {
        $this->level = $level;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
        $subjects = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->where('subjects_mpu.level', '=', $this->level)
                    ->where('subjects_mpu.status_subject','=','Active')
                    ->orderBy('subjects_mpu.subject_type')
                    ->get();
        return view('exports.MPUSubject', [
            'subjects' => $subjects
        ]);
    }
}
