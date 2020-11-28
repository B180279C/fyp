<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action_V_A extends Model
{
   protected $table = 'action_v_a';

	protected $primaryKey = 'action_id';
	
    protected $fillable = [
        'course_id','action_type', 'status','for_who','remarks'
    ];
}
