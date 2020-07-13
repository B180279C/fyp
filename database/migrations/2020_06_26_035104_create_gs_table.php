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
        Schema::create('General_Studies', function (Blueprint $table) {
            $table->id('gs_id');
            $table->string('level');
            $table->string('subject_code');
            $table->string('subject_name');
            $table->string('subject_type');
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
        Schema::dropIfExists('GeneralStudies');
    }
}
