<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id('subscriber_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('subscriber_status_id')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('subscriber_status_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribers');
    }
};
