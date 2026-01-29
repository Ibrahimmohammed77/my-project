<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id('commission_id');
            $table->unsignedBigInteger('studio_id');
            $table->unsignedBigInteger('storage_library_id')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('transaction_type_id'); 
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('studio_share', 10, 2);
            $table->decimal('platform_share', 10, 2);
            $table->date('settlement_date')->nullable();
            $table->unsignedBigInteger('commission_status_id')->nullable();
            $table->timestamps();
            $table->foreign('studio_id')->references('studio_id')->on('studios')->onDelete('cascade');
            $table->foreign('storage_library_id')->references('storage_library_id')->on('storage_libraries')->onDelete('set null');
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
            $table->foreign('transaction_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('commission_status_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commissions');
    }
};
