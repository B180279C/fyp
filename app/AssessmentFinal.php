<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentFinal extends Model
{
   protected $table = 'assessment_final';

	protected $primaryKey = 'ass_fx_id';
	
    protected $fillable = [
        'course_id','ass_fx_type', 'ass_fx_name','ass_fx_document','ass_fx_word','status'
    ];
}
