<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id('plan_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('storage_limit');
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->integer('max_albums')->default(0);
            $table->integer('max_cards')->default(0);
            $table->integer('max_users')->default(0);
            $table->integer('max_offices')->default(0);
            $table->json('features')->nullable();
            $table->unsignedBigInteger('billing_cycle_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('billing_cycle_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
