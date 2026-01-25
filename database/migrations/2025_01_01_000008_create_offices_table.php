<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id('office_id');
            $table->unsignedBigInteger('studio_id');
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('studio_id')->references('studio_id')->on('studios')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('subscriber_id')->on('subscribers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offices');
    }
};
