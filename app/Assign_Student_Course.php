<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assign_Student_Course extends Model
{
   protected $table = 'assign_student_course';

	protected $primaryKey = 'asc_id';
	
    protected $fillable = [
        'course_id','student_id','status'
    ];
}
