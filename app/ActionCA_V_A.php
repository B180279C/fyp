<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionCA_V_A extends Model
{
   protected $table = 'actionca_v_a';

	protected $primaryKey = 'actionCA_id';
	
    protected $fillable = [
        'course_id', 'status','for_who','AccOrRec','self_declaration','suggest','remarks','moderator_date','verified_date'
    ];
}
