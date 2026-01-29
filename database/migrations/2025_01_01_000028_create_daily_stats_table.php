<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id('stat_id');
            $table->date('stat_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('new_users')->default(0);
            $table->integer('new_photos')->default(0);
            $table->integer('photo_views')->default(0);
            $table->integer('card_activations')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['stat_date', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_stats');
    }
};
