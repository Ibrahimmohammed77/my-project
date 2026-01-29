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
            $table->bigInteger('storage_limit')->default(0)->after('description')->comment('Storage limit in bytes. 0 means no specific limit.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_libraries', function (Blueprint $table) {
            $table->dropColumn('storage_limit');
        });
    }
};
