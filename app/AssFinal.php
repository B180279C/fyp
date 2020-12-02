<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssFinal extends Model
{
   protected $table = 'ass_final';

	protected $primaryKey = 'fx_id';
	
    protected $fillable = [
        'course_id','coursework','coursemark','CLO','topic', 'assessment_name','status'
    ];
}

