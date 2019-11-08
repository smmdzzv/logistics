<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoredItemInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stored_item_infos', function (Blueprint $table) {
            $table->char('id',26)->primary();
            $table->char('item_id',26);
            $table->char('ownerId',26);
            $table->char('branch_id',26);
            $table->char('order_id',26);
            $table->char('customs_code_id',26);
            $table->integer('count');
            $table->double('weight');
            $table->double('height');
            $table->double('width');
            $table->double('length');
            $table->double('placeCount');
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
        Schema::dropIfExists('stored_item_infos');
    }
}
