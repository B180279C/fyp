<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teaching_Plan extends Model
{
   protected $table = 'teaching_plan';

	protected $primaryKey = 'tp_id';
	
    protected $fillable = [
        'course_id','week','tutorial','assessment','remarks'
    ];
}
