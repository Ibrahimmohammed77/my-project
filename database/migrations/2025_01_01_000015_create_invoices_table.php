<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('invoice_number', 50)->unique();
            $table->unsignedBigInteger('subscriber_type_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->char('currency', 3)->default('SAR');
            $table->unsignedBigInteger('invoice_status_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('invoice_status_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('payment_method_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('subscriber_type_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
