<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_consumptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_id')->index();
            $table->uuid('destination_id');
            $table->double('forEmpty');
            $table->double('forLoaded');
            $table->double('forEmptyTrailer');
            $table->double('forLoadedTrailer');
            $table->userStamp();
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
        Schema::dropIfExists('fuel_consumptions');
    }
}
