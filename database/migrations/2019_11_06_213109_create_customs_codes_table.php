<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomsCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customs_codes', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('name', 255);
            $table->char('description', 255)->nullable();
            $table->char('internationalName', 255)->nullable();
            $table->char('code', 20)->unique();
            $table->double('price');
            $table->double('interestRate');
            $table->double('vat');
            $table->double('totalRate');
            $table->boolean('isCalculatedByPiece');

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
        Schema::dropIfExists('customs_codes');
    }
}
