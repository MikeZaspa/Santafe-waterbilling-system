<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // In your Admin model migration
        public function up()
        {
            Schema::table('admins', function (Blueprint $table) {
                $table->boolean('two_factor_enabled')->default(false);
                $table->string('two_factor_code')->nullable();
                $table->timestamp('two_factor_expires_at')->nullable();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            //
        });
    }
};
