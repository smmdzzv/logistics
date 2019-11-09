<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoredItemTripHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stored_item_trip_histories', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('stored_item_id', 26);
            $table->char('trip_id', 26)->index();
            $table->char('registered_by_id', 26);
            $table->char('deleted_by_id', 26)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('stored_item_trip_histories');
    }
}
