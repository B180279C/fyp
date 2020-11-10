<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentFinalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_final', function (Blueprint $table) {
            $table->id('ass_fx_id');
            $table->string('course_id');
            $table->string('ass_fx_type');
            $table->string('ass_fx_name');
            $table->text('ass_fx_document')->nullable();
            $table->text('ass_fx_word')->nullable();
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
        Schema::dropIfExists('assessment_final');
    }
}
