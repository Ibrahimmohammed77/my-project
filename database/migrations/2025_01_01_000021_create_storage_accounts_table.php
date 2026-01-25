<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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
    }
};
