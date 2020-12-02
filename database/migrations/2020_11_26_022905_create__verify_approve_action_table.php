<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyApproveActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Action_V_A', function (Blueprint $table) {
            $table->id('action_id');
            $table->string('course_id');
            $table->string('action_type');
            $table->string('status');
            $table->string('for_who');
            $table->text('remarks')->nullable();
            $table->date('prepared_date')->nullable();
            $table->date('verified_date')->nullable();
            $table->date('approved_date')->nullable();
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
        Schema::dropIfExists('Action_V_A');
    }
}
