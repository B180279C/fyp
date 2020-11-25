<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TP_CQI extends Model
{
   protected $table = 'tp_cqi';

	protected $primaryKey = 'CQI_id';
	
    protected $fillable = [
        'course_id','action','plan','status'
    ];
}
