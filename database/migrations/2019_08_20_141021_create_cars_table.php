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
            $table->uuid('id')->primary();;
            $table->string('number', 20)->unique();
            $table->double('length', 10)->nullable();
            $table->double('height', 10)->nullable();
            $table->double('width', 10)->nullable();
            $table->double('maxCubage', 10);
            $table->double('maxWeight', 10);
            $table->double('fuelAmount')->default(0);
            $table->string('serial', 40)->unique()->nullable();
            $table->string('trailerNumber', 20)->unique()->nullable();
            $table->double('trailerMaxCubage', 10)->nullable();
            $table->double('trailerMaxWeight', 10)->nullable();
            $table->timestamps();
            $table->userStamp();

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
