<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
   protected $table = 'semesters';

	protected $primaryKey = 'semester_id';
	
    protected $fillable = [
        'semester', 'year','startDate','endDate'
    ];
}
