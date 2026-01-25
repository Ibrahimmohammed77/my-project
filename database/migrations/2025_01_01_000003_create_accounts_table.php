<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('password_hash', 255);
            $table->string('full_name', 100);
            $table->string('phone', 20)->unique();
            $table->string('profile_image', 255)->nullable();
            $table->unsignedBigInteger('account_status_id')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->string('verification_code', 10)->nullable();
            $table->timestamp('verification_expiry')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_status_id')->references('lookup_value_id')->on('lookup_values');
            $table->index('account_status_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
