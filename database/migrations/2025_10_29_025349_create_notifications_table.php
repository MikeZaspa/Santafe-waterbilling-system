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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->constrained('admin_consumers')->onDelete('cascade');
            $table->foreignId('billing_id')->nullable()->constrained('accountant_billings')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('billing'); // billing, payment, system, etc.
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};