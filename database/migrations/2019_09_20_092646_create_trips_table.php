<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->char('id', 26)->primary();
            $table->char('carId', 26);
            $table->char('driverId', 26);
            $table->char('to_consumption_id', 26);
            $table->char('from_consumption_id', 26);
            $table->string('code', 20)->unique();
            $table->char('status', 15);
            $table->date('departureDate');
            $table->date('returnDate');
            $table->boolean('hasTrailer');
            $table->date('departureAt')->nullable();
            $table->date('returnedAt')->nullable();
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
