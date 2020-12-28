<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

	protected $primaryKey = 'department_id';
	
    protected $fillable = [
        'department_name','faculty_id','status_department'
    ];
}
