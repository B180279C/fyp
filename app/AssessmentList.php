<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentList extends Model
{
   protected $table = 'assessment_list';

	protected $primaryKey = 'ass_li_id';
	
    protected $fillable = [
        'ass_id','ass_type', 'ass_name','ass_document','ass_word','status'
    ];
}

