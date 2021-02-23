<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('carId')->index();
            $table->uuid('driverId')->index();
            $table->uuid('departure_branch_id');
            $table->uuid('destination_branch_id');
//            $table->boolean('emptyToDestination');
//            $table->boolean('emptyFromDestination');
            $table->integer('routeLengthToDestination');
            $table->integer('routeLengthWithCargoTo');
            $table->integer('routeLengthFromDestination');
            $table->integer('cargoWeightTo');
            $table->integer('cargoWeightFrom');
            $table->integer('trailerCargoWeightTo');
            $table->integer('trailerCargoWeightFrom');
            $table->integer('mileageBefore');
            $table->integer('mileageAfter');
            $table->uuid('to_consumption_id');
            $table->uuid('from_consumption_id');
            $table->string('code', 20)->unique();
            $table->char('status', 15);
            $table->date('departureDate');
            $table->date('returnDate');
            $table->boolean('hasTrailer');
            $table->date('departureAt')->nullable();
            $table->date('returnedAt')->nullable();
            $table->double('contractPrice')->default(0);
            $table->double('driverSalary')->default(0);
            $table->double('tripCoast')->default(0);
            $table->double('otherExpanses')->default(0);
            $table->double('fine')->default(0);
            $table->double('totalFuelConsumption')->default(0);
            $table->double('fuelAmount')->default(0);
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
        Schema::dropIfExists('trips');
    }
}
