<?php

namespace App\Exports;

use App\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class SubjectExport implements FromView
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
        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $this->id)
                    ->orderBy('subjects.subject_type')
                    ->get();
        return view('exports.Subject', [
            'subjects' => $subjects
        ]);
    }
}
