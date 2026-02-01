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
        Schema::table('cards', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_library_id')->nullable()->after('card_group_id');
            
            $table->foreign('storage_library_id')
                  ->references('storage_library_id')
                  ->on('storage_libraries')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['storage_library_id']);
            $table->dropColumn('storage_library_id');
        });
    }
};
