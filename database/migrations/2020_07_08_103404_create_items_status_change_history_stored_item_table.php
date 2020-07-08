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
            $table->char('id', 26)->primary();
            $table->char('items_status_change_history_id', 26);
            $table->char('stored_item_id', 26);
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
