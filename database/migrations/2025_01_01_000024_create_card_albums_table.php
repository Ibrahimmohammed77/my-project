<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('card_albums', function (Blueprint $table) {
            $table->id('card_album_id');
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('album_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['card_id', 'album_id']);
            $table->foreign('card_id')->references('card_id')->on('cards')->onDelete('cascade');
            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('card_albums');
    }
};
