<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_topics', function (Blueprint $table) {
            $table->id('topic_id');
            $table->string('tp_id');
            $table->string('lecture_topic');
            $table->string('lecture_hour');
            $table->text('sub_topic');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Plan_Topics');
    }
}
