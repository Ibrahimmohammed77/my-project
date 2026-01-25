<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Card Groups
        Schema::create('card_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Cards
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->char('card_uuid', 36)->unique(); // Assuming manual UUID generation in generic defaults
            $table->string('card_number', 50);
            $table->unsignedBigInteger('card_group_id')->nullable();
            $table->string('owner_type', 50);
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('holder_id')->nullable();
            $table->unsignedBigInteger('card_type_id');
            $table->unsignedBigInteger('card_status_id')->nullable();
            $table->timestamp('activation_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('card_group_id')->references('group_id')->on('card_groups')->onDelete('set null');
            $table->foreign('holder_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('card_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('card_status_id')->references('lookup_value_id')->on('lookup_values');

            $table->index(['owner_type', 'owner_id']);
        });

        // Card Albums
        Schema::create('card_albums', function (Blueprint $table) {
            $table->id('card_album_id');
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('album_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['card_id', 'album_id']);
            $table->foreign('card_id')->references('card_id')->on('cards')->onDelete('cascade');
            $table->foreign('album_id')->references('album_id')->on('albums')->onDelete('cascade');
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('account_id');
            $table->string('title', 255);
            $table->text('message');
            $table->unsignedBigInteger('notification_type_id');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('read_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('notification_type_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('action', 100);
            $table->string('resource_type', 50)->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
        });

        // Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->string('setting_key', 100)->unique();
            $table->json('setting_value');
            $table->unsignedBigInteger('setting_type_id');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('setting_type_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Daily Stats
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id('stat_id');
            $table->date('stat_date');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->integer('new_accounts')->default(0);
            $table->integer('new_photos')->default(0);
            $table->integer('photo_views')->default(0);
            $table->integer('card_activations')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['stat_date', 'account_id']);
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_stats');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('card_albums');
        Schema::dropIfExists('cards');
        Schema::dropIfExists('card_groups');
    }
};
