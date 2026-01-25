<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Studios
        Schema::create('studios', function (Blueprint $table) {
            $table->id('studio_id');
            $table->unsignedBigInteger('account_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->unsignedBigInteger('studio_status_id')->nullable();
            
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            
            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('studio_status_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Schools
        Schema::create('schools', function (Blueprint $table) {
            $table->id('school_id');
            $table->unsignedBigInteger('account_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->unsignedBigInteger('school_type_id')->nullable();
            $table->unsignedBigInteger('school_level_id')->nullable();
            $table->unsignedBigInteger('school_status_id')->nullable();

            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();

            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('school_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('school_level_id')->references('lookup_value_id')->on('lookup_values');
            $table->foreign('school_status_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Subscribers
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id('subscriber_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('subscriber_status_id')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('subscriber_status_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->unsignedBigInteger('account_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth')->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            
            $table->json('settings')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('gender_id')->references('lookup_value_id')->on('lookup_values');
        });

        // Offices
        Schema::create('offices', function (Blueprint $table) {
            $table->id('office_id');
            $table->unsignedBigInteger('studio_id');
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('studio_id')->references('studio_id')->on('studios')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('subscriber_id')->on('subscribers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offices');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('schools');
        Schema::dropIfExists('studios');
    }
};
