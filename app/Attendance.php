<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
   protected $table = 'attendance';

	protected $primaryKey = 'attendance_id';
	
    protected $fillable = [
        'tt_id','weekly','hour','A_week','A_date','less_hour','students_status','code','code_active_time'
    ];
}
