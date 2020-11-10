<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentFinalResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_final_result', function (Blueprint $table) {
            $table->id('fxr_id');
            $table->string('course_id');
            $table->string('student_id');
            $table->string('submitted_by');
            $table->string('document_name');
            $table->string('document');
            $table->string('status');
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
        Schema::dropIfExists('assessment_final_result');
    }
}
