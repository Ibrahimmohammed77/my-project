<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id('school_id');
            $table->unsignedBigInteger('user_id');
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->unsignedBigInteger('school_type_id')->nullable();
            $table->unsignedBigInteger('school_level_id')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->json('settings')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('school_level_id')->references('lookup_value_id')->on('lookup_values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
