<?php

namespace App\Imports;

use App\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class syllabusRead implements ToModel
{
    /**
    * @param Collection $collection
    */
    use Importable;

    public function model(array $row)
    {
        return new Subject();
    }
}
