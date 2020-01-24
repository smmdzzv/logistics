<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('tariffId', 26);
            $table->string('name', 255)->unique();
            $table->char('unit', 10);
            $table->boolean('onlyCustomPrice')->default(false);
            $table->boolean('onlyAgreedPrice')->default(false);
            $table->boolean('applyDiscount')->default(false);
            $table->boolean('calculateByNormAndWeight')->default(false);
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
        Schema::dropIfExists('items');
    }
}
