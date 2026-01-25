<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id('subscription_id');
            $table->unsignedBigInteger('subscriber_type_id'); 
            $table->unsignedBigInteger('subscriber_id'); 
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
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
