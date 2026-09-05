<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20)->default('android')->index();
            $table->string('version', 30)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device_type', 30)->nullable()->index();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->nullableTimestamps();
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_downloads');
    }
};