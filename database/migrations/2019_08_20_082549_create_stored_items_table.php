<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoredItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stored_items', function (Blueprint $table) {
            $table->char('id',26)->primary();
            $table->char('stored_item_info_id',26)->index();
            $table->char('code', 20)->unique();
//            $table->char('deleted_by_id', 26)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('stored_items');
    }
}
