<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Albums
        Schema::create('albums', function (Blueprint $table) {
            $table->id('album_id');
            $table->string('owner_type', 50); // Polymorphic
            $table->unsignedBigInteger('owner_id');
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
        });

        // Photos
        Schema::create('photos', function (Blueprint $table) {
            $table->id('photo_id');
            $table->unsignedBigInteger('album_id');
            $table->string('original_name', 255);
            $table->string('stored_name', 255)->unique();
            $table->string('file_path', 500);
            $table->bigInteger('file_size');
            $table->string('mime_type', 100)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->text('caption')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('cascade');
        });

        // Storage Accounts
        Schema::create('storage_accounts', function (Blueprint $table) {
            $table->id('storage_account_id');
            $table->string('owner_type', 50);
            $table->unsignedBigInteger('owner_id');
            $table->integer('total_space');
            $table->integer('used_space')->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['owner_type', 'owner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('storage_accounts');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('albums');
    }
};
