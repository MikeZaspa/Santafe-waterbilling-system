<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_logs', 'municipality')) {
                $table->string('municipality')->nullable()->after('city');
            }

            if (!Schema::hasColumn('admin_logs', 'street')) {
                $table->string('street')->nullable()->after('municipality');
            }

            if (!Schema::hasColumn('admin_logs', 'barangay')) {
                $table->string('barangay')->nullable()->after('street');
            }

            if (!Schema::hasColumn('admin_logs', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('timezone');
            }

            if (!Schema::hasColumn('admin_logs', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $columnsToDrop = [];

            foreach (['municipality', 'street', 'barangay', 'latitude', 'longitude'] as $column) {
                if (Schema::hasColumn('admin_logs', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
