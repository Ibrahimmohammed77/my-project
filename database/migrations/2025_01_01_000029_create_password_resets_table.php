<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id('reset_id');
            $table->unsignedBigInteger('account_id');
            $table->string('reset_code', 10);
            $table->string('contact_method', 20); // 'email' or 'phone'
            $table->string('contact_value', 100); // البريد أو رقم الهاتف
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->index('account_id');
            $table->index('reset_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};
