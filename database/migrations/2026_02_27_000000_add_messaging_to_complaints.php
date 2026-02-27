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
        // Add columns to complaints table
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('consumer_id');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open')->after('attachment_path');
            $table->timestamp('last_message_at')->nullable()->after('status');
        });

        // Create complaint_messages table for conversation thread
        Schema::create('complaint_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->enum('sender_type', ['consumer', 'admin'])->default('consumer');
            $table->string('sender_name');
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['complaint_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_messages');
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['subject', 'status', 'last_message_at']);
        });
    }
};
