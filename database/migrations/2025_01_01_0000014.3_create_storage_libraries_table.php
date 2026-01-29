<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('storage_libraries', function (Blueprint $table) {
            $table->id('storage_library_id');
            $table->unsignedBigInteger('studio_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('studio_id')->references('studio_id')->on('studios')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('storage_libraries');
    }
};
