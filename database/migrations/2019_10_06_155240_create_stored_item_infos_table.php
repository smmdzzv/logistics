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
            $table->uuid('id')->primary();
            $table->uuid('item_id')->index();
            $table->uuid('owner_id')->index();
//            $table->uuid('deleted_by_id')->index()->nullable();
            $table->uuid('branch_id')->index();
            $table->uuid('order_id')->index();
            $table->char('shop',50)->nullable();
            $table->uuid('customs_code_tax_id')->index();
            $table->uuid('customs_code_id')->index();
            $table->uuid('tariff_id')->index();
//            $table->uuid('tariff_price_history_id',26)->index();
            $table->integer('count');
            $table->integer('placeCount');
            $table->double('weight',10, 3);
            $table->double('height',10, 3);
            $table->double('width',10, 3);
            $table->double('length',10, 3);
            $table->string('status')->default('active');


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
