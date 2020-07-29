<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacPortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculty_portfolio', function (Blueprint $table) {
            $table->id('fp_id');
            $table->string('faculty_id');
            $table->string('portfolio_type');
            $table->string('portfolio_name');
            $table->string('portfolio_place');
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
        Schema::dropIfExists('faculty_portfolio');
    }
}
