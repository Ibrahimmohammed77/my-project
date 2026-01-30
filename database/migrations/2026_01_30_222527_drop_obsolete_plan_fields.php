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
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['billing_cycle_id']);
            $table->dropColumn([
                'max_albums',
                'max_cards',
                'max_users',
                'max_storage_libraries',
                'billing_cycle_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('max_albums')->default(0);
            $table->integer('max_cards')->default(0);
            $table->integer('max_users')->default(0);
            $table->integer('max_storage_libraries')->default(0);
            $table->unsignedBigInteger('billing_cycle_id')->nullable();
            
            $table->foreign('billing_cycle_id')->references('lookup_value_id')->on('lookup_values');
        });
    }
};
