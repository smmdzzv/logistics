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
            $table->char('id',26)->primary();;
            $table->char('item_id',26);
            $table->char('owner_id',26);
            $table->char('branch_id',26);
            $table->char('order_id',26);
//            $table->unsignedBigInteger('billingInfo_id');
            $table->double('weight');
            $table->double('height');
            $table->double('width');
            $table->double('length');
            $table->integer('count');
            $table->timestamps();

            $table->index('owner_id');
            $table->index('branch_id');
            $table->index('order_id');
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
