<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentResultStudent extends Model
{
   protected $table = 'assessment_result_students';

	protected $primaryKey = 'ar_stu_id';
	
    protected $fillable = [
        'ass_id','student_id', 'submitted_by','document_name','document','status'
    ];
}
