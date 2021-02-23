<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();;
            $table->uuid('owner_id')->index();
            $table->uuid('branch_id');
            $table->string('status')->default('active');
            $table->decimal('totalCount', 10, 2);
            $table->decimal('totalWeight', 10, 3);
            $table->decimal('totalCubage', 10, 3);
            $table->decimal('totalPrice', 10, 2);
            $table->decimal('totalDiscount', 10, 2);
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
        Schema::dropIfExists('orders');
    }
}
