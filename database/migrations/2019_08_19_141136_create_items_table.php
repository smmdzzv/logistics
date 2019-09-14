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
            $table->char('tariff_id', 26);
            $table->string('name', 255);
            $table->string('unit', 15);
            $table->boolean('isPriceCustom')->default(false);
            $table->timestamps();

            $table->index('tariff_id');
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
