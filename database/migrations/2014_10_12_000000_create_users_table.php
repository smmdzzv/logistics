<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('position_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('name')->nullable();
            $table->string('code')->unique()->index()->nullable();
            $table->string('email')->unique()->index()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('timezone')->nullable();
            $table->string('api_token', 80)
                ->unique()
                ->nullable()
                ->default(null);
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->userStamp();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
