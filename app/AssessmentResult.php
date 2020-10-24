<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
   protected $table = 'assessment_results';

	protected $primaryKey = 'ass_rs_id';
	
    protected $fillable = [
        'course_id','assessment', 'submission_name','status'
    ];
}
