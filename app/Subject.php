<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

	protected $primaryKey = 'subject_id';
	
    protected $fillable = [
        'programme_id','subject_code', 'subject_name', 'subject_type','syllabus'
    ];
}
