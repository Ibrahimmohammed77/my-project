<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Accounts
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->string('username', 50);
            $table->string('email', 100)->unique()->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('password_hash', 255); // Laravel uses 'password' usually, but following schema
            $table->string('full_name', 100);
            $table->string('phone', 20)->unique();
            $table->string('profile_image', 255)->nullable();
            $table->unsignedBigInteger('account_status_id')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->string('verification_code', 10)->nullable();
            $table->timestamp('verification_expiry')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_status_id')->references('lookup_value_id')->on('lookup_values');
            $table->index('account_status_id');
        });

        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('name', 50)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        // Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('name', 100)->unique();
            $table->string('resource_type', 50);
            $table->string('action', 50);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['resource_type', 'action'], 'unique_permission_resource_action');
        });

        // Role Permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id('role_permission_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['role_id', 'permission_id'], 'unique_role_permission');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('permission_id')->on('permissions')->onDelete('cascade');
        });

        // Account Roles
        Schema::create('account_roles', function (Blueprint $table) {
            $table->id('account_role_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['account_id', 'role_id'], 'unique_account_role_scope');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('accounts');
    }
};
