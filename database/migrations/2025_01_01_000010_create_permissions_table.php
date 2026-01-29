<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('name', 100)->unique();
            $table->string('resource_type', 50);
            $table->string('action', 50);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['resource_type', 'action'], 'unique_permission_resource_action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
