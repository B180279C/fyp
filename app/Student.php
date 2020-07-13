<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'programme_id', 'year', 'semester', 'intake' , 'student_id','student_image'
    ]; 
}
