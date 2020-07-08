<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientItemsSelectionStoredItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_items_selection_stored_item', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('client_items_selection_id', 26);
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
        Schema::dropIfExists('client_items_selection_stored_item');
    }
}
