<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionFA_V_A extends Model
{
   protected $table = 'actionfa_v_a';

	protected $primaryKey = 'actionFA_id';
	
    protected $fillable = [
        'course_id', 'status','for_who','degree','self_declaration','suggest','feedback','remarks','moderator_date','verified_date','self_date','approved_date','remarks_dean'
    ];
}
