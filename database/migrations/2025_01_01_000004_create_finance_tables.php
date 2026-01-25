<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Plans
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

        // Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id('subscription_id');
            $table->unsignedBigInteger('subscriber_type_id'); // From Lookups
            $table->unsignedBigInteger('subscriber_id'); // ID of School/Studio/etc.
            $table->unsignedBigInteger('plan_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('renewal_date');
            $table->boolean('auto_renew')->default(true);
            $table->unsignedBigInteger('subscription_status_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('subscriber_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('plan_id')->references('plan_id')->on('plans');
            $table->foreign('subscription_status_id')->references('lookup_value_id')->on('lookup_values');
            
            // Note: subscriber_id refers to different tables based on subscriber_type_id logic (not simple foreign key)
        });

        // Invoices
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

        // Invoice Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('invoice_id');
            $table->string('description', 255);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->unsignedBigInteger('item_type_id');
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('taxable')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
            $table->foreign('item_type_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Payments
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

        // Commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id('commission_id');
            $table->unsignedBigInteger('studio_id');
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('transaction_type_id'); 
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('studio_share', 10, 2);
            $table->decimal('platform_share', 10, 2);
            $table->date('settlement_date')->nullable();
            $table->unsignedBigInteger('commission_status_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('studio_id')->references('studio_id')->on('studios')->onDelete('cascade');
            $table->foreign('office_id')->references('office_id')->on('offices')->onDelete('set null');
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
            $table->foreign('transaction_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('commission_status_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
