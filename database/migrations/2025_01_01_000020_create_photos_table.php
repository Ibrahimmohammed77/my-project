<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('photos');
    }
};
