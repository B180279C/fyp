<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan_Topic extends Model
{
   protected $table = 'plan_topics';

	protected $primaryKey = 'topic_id';
	
    protected $fillable = [
        'tp_id','lecture_topic','lecture_hour','sub_topic'
    ];
}
