<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->char('card_uuid', 36)->unique(); 
            $table->string('card_number', 50);
            $table->unsignedBigInteger('card_group_id')->nullable();
            $table->string('owner_type', 50);
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('holder_id')->nullable();
            $table->unsignedBigInteger('card_type_id');
            $table->unsignedBigInteger('card_status_id')->nullable();
            $table->timestamp('activation_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('card_group_id')->references('group_id')->on('card_groups')->onDelete('set null');
            $table->foreign('holder_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('card_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('card_status_id')->references('lookup_value_id')->on('lookup_values');

            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
