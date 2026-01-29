<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('card_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('sub_card_available')->default(0);
            $table->integer('sub_card_used')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('card_groups');
    }
};
