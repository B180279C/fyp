<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    protected $table = 'programmes';

	protected $primaryKey = 'programme_id';
	
    protected $fillable = [
        'programme_name','short_form_name', 'department_id','level','status_programme'
    ];
}
