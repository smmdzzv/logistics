<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomsCodeTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customs_code_taxes', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('customs_code_id', 26)->index();
            $table->double('price',2);
            $table->double('interestRate');
            $table->double('vat');
            $table->double('totalRate');
            $table->boolean('isCalculatedByPiece');
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
        Schema::dropIfExists('customs_code_taxes');
    }
}
