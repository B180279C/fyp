<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaActionVATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actionfa_v_a', function (Blueprint $table) {
            $table->id('actionFA_id');
            $table->string('course_id');
            $table->string('status');
            $table->string('for_who');
            $table->string('degree')->nullable();
            $table->string('self_declaration')->nullable();
            $table->text('suggest')->nullable();
            $table->text('feedback')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('actionfa_v_a');
    }
}
