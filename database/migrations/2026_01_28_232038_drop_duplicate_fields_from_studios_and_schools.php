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
            $table->dropColumn(['name', 'email', 'phone']);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->string('name', 100)->nullable(); // Restoring as nullable to avoid data issues
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
        });
    }
};
