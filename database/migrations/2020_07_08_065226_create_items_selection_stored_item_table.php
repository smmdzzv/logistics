<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsSelectionStoredItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_selection_stored_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('items_selection_id')->index();
            $table->uuid('stored_item_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_items_selection_stored_item');
    }
}
