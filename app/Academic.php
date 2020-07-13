<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Academic extends Model
{
    protected $table = 'academic';

	protected $primaryKey = 'academic_id';

    protected $fillable = [
        'academic_name'
    ]; 
}
