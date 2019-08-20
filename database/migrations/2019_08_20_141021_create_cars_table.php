<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number', 20)->unique();
            $table->string('trailerNumber', 20)->unique();
            $table->double('length', 10)->nullable();
            $table->double('height', 10)->nullable();
            $table->double('width', 10)->nullable();
            $table->string('serial', 30)->unique()->nullable();
            $table->integer('fuel')->default(0);
            $table->timestamps();

            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
