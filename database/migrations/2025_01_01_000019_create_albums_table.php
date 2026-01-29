<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id('album_id');
            $table->string('owner_type', 50); // Polymorphic
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('storage_library_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->integer('view_count')->default(0);
            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['owner_type', 'owner_id']);
            $table->foreign('storage_library_id')->references('storage_library_id')->on('storage_libraries')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('albums');
    }
};
