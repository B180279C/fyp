<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->id('id');
            $table->string('user_id');
            $table->string('staff_id');
            $table->string('department_id');
            $table->string('faculty_id');
            $table->text('staff_image')->nullable();
            $table->text('lecturer_CV')->nullable();
            $table->text('staff_sign')->nullable();
            $table->string('status_staff')->default('Active');
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
        Schema::dropIfExists('staffs');
    }
}
