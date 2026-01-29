<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 50)->unique();
            $table->string('email', 180)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('password');
            $table->string('phone', 13)->unique();
            $table->boolean('phone_verified')->default(false);
            $table->string('profile_image', 255)->nullable();
            $table->unsignedBigInteger('user_status_id')->nullable();
            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->string('verification_code', 10)->nullable();
            $table->timestamp('verification_expiry')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_active')->default(false);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_status_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('user_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->index('user_status_id');
            $table->index('user_type_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
