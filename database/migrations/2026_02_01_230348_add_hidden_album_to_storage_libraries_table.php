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
        Schema::table('storage_libraries', function (Blueprint $table) {
            $table->unsignedBigInteger('hidden_album_id')->nullable()->after('description');
            
            $table->foreign('hidden_album_id')
                  ->references('album_id')
                  ->on('albums')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_libraries', function (Blueprint $table) {
            $table->dropForeign(['hidden_album_id']);
            $table->dropColumn('hidden_album_id');
        });
    }
};
