<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropForeign(['studio_status_id']);
            $table->dropColumn('studio_status_id');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['school_status_id']);
            $table->dropColumn('school_status_id');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropForeign(['subscriber_status_id']);
            $table->dropColumn('subscriber_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->unsignedBigInteger('studio_status_id')->nullable();
            $table->foreign('studio_status_id')->references('lookup_value_id')->on('lookup_values');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->unsignedBigInteger('school_status_id')->nullable();
            $table->foreign('school_status_id')->references('lookup_value_id')->on('lookup_values');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->unsignedBigInteger('subscriber_status_id')->nullable();
            $table->foreign('subscriber_status_id')->references('lookup_value_id')->on('lookup_values');
        });
    }
};
