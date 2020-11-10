<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentFinalResult extends Model
{
   protected $table = 'assessment_final_result';

	protected $primaryKey = 'fxr_id';
	
    protected $fillable = [
        'fx_id','student_id', 'submitted_by','document_name','document','status'
    ];
}
