<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
   protected $table = 'timetable';

	protected $primaryKey = 'tt_id';
	
    protected $fillable = [
        'course_id','week','class_hour','F_or_H','status'
    ];
}
