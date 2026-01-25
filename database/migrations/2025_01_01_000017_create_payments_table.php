<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('payment_method_id');
            $table->string('gateway_transaction_id', 100)->nullable();
            $table->json('gateway_response')->nullable();
            $table->unsignedBigInteger('payment_status_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('payment_status_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
