<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects_mpu', function (Blueprint $table) {
            $table->id('mpu_id');
            $table->string('level');
            $table->string('subject_code');
            $table->string('subject_name');
            $table->string('subject_type');
            $table->text('syllabus')->nullable();
            $table->string('syllabus_name');
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
        Schema::dropIfExists('subjects_mpu');
    }
}
