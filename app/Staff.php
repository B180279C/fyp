<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
	protected $table = 'staffs';

	protected $primaryKey = 'id';
	
    protected $fillable = [
        'user_id','staff_id', 'department_id','academic_id','staff_image'
    ];
}
