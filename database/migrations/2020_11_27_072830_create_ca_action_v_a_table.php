<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaActionVATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actionCA_v_a', function (Blueprint $table) {
            $table->id('actionCA_id');
            $table->string('course_id');
            $table->string('status');
            $table->string('for_who');
            $table->string('AccOrRec');
            $table->string('self_declaration');
            $table->text('suggest')->nullable();
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
        Schema::dropIfExists('actionCA_v_a');
    }
}
