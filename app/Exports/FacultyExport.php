<?php

namespace App\Exports;

use App\Faculty;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class FacultyExport implements FromView
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function view() : View
    {
        $facultys = Faculty::all()->toArray();
        return view('exports.Faculty', [
            'facultys' => $facultys
        ]);
    }
}
