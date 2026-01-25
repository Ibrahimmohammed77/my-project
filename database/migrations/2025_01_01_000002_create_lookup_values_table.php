<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lookup_values', function (Blueprint $table) {
            $table->id('lookup_value_id');
            $table->unsignedBigInteger('lookup_master_id');
            $table->string('code', 50);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['lookup_master_id', 'code'], 'unique_lookup_code');
            $table->foreign('lookup_master_id')->references('lookup_master_id')->on('lookup_masters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lookup_values');
    }
};
