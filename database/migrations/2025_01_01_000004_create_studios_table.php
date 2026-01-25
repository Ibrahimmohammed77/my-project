<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id('studio_id');
            $table->unsignedBigInteger('account_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->unsignedBigInteger('studio_status_id')->nullable();
            
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            
            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('studio_status_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('studios');
    }
};
