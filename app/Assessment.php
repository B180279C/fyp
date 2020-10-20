<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
   protected $table = 'assessments';

	protected $primaryKey = 'ass_id';
	
    protected $fillable = [
        'course_id','ass_type','assessment', 'ass_name','ass_place','ass_document','ass_word','status'
    ];
}
