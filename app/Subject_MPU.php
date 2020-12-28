<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject_MPU extends Model
{
    protected $table = 'subjects_mpu';

	protected $primaryKey = 'mpu_id';
	
    protected $fillable = [
        'level','subject_code', 'subject_name', 'subject_type','syllabus','syllabus_name','status_subject'
    ];
}
