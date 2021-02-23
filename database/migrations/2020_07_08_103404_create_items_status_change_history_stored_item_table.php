<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsStatusChangeHistoryStoredItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_status_change_history_stored_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('items_status_change_history_id');
            $table->uuid('stored_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_status_change_history_stored_item');
    }
}
