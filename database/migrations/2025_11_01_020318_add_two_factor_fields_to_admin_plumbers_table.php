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
        Schema::table('admin_plumbers', function (Blueprint $table) {
            // Add email column if it doesn't exist
            if (!Schema::hasColumn('admin_plumbers', 'email')) {
                $table->string('email')->unique()->after('username');
            }
            
            // Add 2FA fields
            $table->string('two_factor_code')->nullable()->after('email');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_plumbers', function (Blueprint $table) {
            $table->dropColumn(['email', 'two_factor_code', 'two_factor_expires_at']);
        });
    }
};