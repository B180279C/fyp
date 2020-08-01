<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty_Portfolio extends Model
{
   protected $table = 'faculty_portfolio';

	protected $primaryKey = 'fp_id';
	
    protected $fillable = [
        'faculty_id','portfolio_type', 'portfolio_name','portfolio_place','portfolio_file','status'
    ];
}
