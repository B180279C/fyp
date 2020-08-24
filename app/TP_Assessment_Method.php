<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TP_Assessment_Method extends Model
{
   protected $table = 'tp_assessment_method';

	protected $primaryKey = 'am_id';
	
    protected $fillable = [
        'course_id','CLO','PO','domain_level','method','assessment','markdown'
    ];
}
