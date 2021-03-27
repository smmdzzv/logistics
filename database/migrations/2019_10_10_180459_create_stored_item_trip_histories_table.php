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
            $table->uuid('id')->primary();
            $table->uuid('stored_item_id');
            $table->uuid('trip_id')->index();
            $table->string('status');
//            $table->$this->uuid('registered_by_id');
//            $table->$this->uuid('deleted_by_id')->nullable();
            $table->uuid('loaded_by_id')->nullable();
            $table->dateTime('loaded_at')->nullable();

            $table->userStamp();
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
