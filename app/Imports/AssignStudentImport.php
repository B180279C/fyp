<?php

namespace App\Imports;

use App\Assign_Student_Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssignStudentImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        return new Assign_Student_Course();
    }
}
