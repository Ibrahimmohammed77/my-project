<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة فهارس للبحث




            $table->index('last_login');
            $table->index(['email_verified', 'email_verified_at']);
            $table->index(['phone_verified', 'verification_expiry']);
            $table->index(['created_at', 'updated_at']);

            // فهارس مركبة للاستعلامات المعقدة
            $table->index(['is_active', 'user_type_id', 'created_at']);
            $table->index(['user_status_id', 'last_login', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {


     
            $table->dropIndex(['last_login']);
            $table->dropIndex(['email_verified', 'email_verified_at']);
            $table->dropIndex(['phone_verified', 'verification_expiry']);
            $table->dropIndex(['created_at', 'updated_at']);
            $table->dropIndex(['is_active', 'user_type_id', 'created_at']);
            $table->dropIndex(['user_status_id', 'last_login', 'is_active']);
        });
    }
};
