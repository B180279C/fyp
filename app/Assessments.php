<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessments extends Model
{
   protected $table = 'assessments';

	protected $primaryKey = 'ass_id';
	
    protected $fillable = [
        'course_id','assessment', 'assessment_name','CLO','coursemark','coursework','sample_stored','status'
    ];
}

