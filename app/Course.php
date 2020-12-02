<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
   protected $table = 'courses';

	protected $primaryKey = 'course_id';
	
    protected $fillable = [
        'subject_id','course_type','semester','credit','lecturer','moderator','verified_by','approved_by','status'
    ];
}
