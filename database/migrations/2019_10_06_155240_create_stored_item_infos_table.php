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
            $table->char('item_id',26)->index();
            $table->char('ownerId',26)->index();
//            $table->char('deleted_by_id',26)->index()->nullable();
            $table->char('branch_id',26)->index();
            $table->char('order_id',26)->index();
            $table->char('shop',50)->nullable();
            $table->char('customs_code_id',26)->index();
            $table->char('tariff_id',26)->index();
            $table->integer('count');
            $table->double('weight',3);
            $table->double('height',2);
            $table->double('width',3);
            $table->double('length',3);
//            $table->double('placeCount');

            $table->userStamp();
            $table->timestamps();
            $table->softDeletes();
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
