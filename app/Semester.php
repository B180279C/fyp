<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
   protected $table = 'semesters';

	protected $primaryKey = 'semester_id';
	
    protected $fillable = [
        'year','semester','semester_name','startDate','endDate','status_sem'
    ];
}
