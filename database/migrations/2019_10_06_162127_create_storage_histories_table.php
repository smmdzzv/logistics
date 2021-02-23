<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('storage_id');
            $table->uuid('stored_item_id');
//            $table->char('registeredById', 26);
//            $table->char('deletedById', 26)->nullable();

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
        Schema::dropIfExists('storage_histories');
    }
}
