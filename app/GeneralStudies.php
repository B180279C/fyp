<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralStudies extends Model
{
    protected $table = 'general_studies';

	protected $primaryKey = 'gs_id';
	
    protected $fillable = [
        'level','subject_code', 'subject_name', 'subject_type'
    ];
}
