<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('ip_address');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable(); // Added region/state
            $table->string('timezone')->nullable(); // Added timezone
            $table->string('browser')->nullable(); // Added browser info
            $table->string('platform')->nullable(); // Added platform/OS
            $table->string('device')->nullable(); // Added device type
            $table->string('activity');
            $table->text('user_agent')->nullable(); // Added full user agent
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('session_duration')->nullable(); // in seconds
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};