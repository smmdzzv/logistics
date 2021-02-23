<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLostStoredItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lost_stored_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stored_item_id')->index();
            $table->uuid('payment_id')->index();

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
        Schema::dropIfExists('lost_stored_items');
    }
}
