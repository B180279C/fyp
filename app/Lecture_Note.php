<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecture_Note extends Model
{
   protected $table = 'lecture_notes';

	protected $primaryKey = 'ln_id';
	
    protected $fillable = [
        'course_id','note_type', 'note_name','note_place','note','used_by','status'
    ];
}
