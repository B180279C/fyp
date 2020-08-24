<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTpAssessmentMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tp_assessment_method', function (Blueprint $table) {
            $table->id('am_id');
            $table->string('course_id');
            $table->string('CLO');
            $table->string('PO')->nullable();
            $table->string('domain_level')->nullable();
            $table->string('method')->nullable();
            $table->string('assessment')->nullable();
            $table->string('markdown')->nullable();
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
        Schema::dropIfExists('tp_assessment_method');
    }
}
