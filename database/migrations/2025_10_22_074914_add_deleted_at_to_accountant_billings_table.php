<?php
// database/migrations/[timestamp]_add_deleted_at_to_accountant_billings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accountant_billings', function (Blueprint $table) {
            $table->softDeletes(); // This adds the deleted_at column
        });
    }

    public function down()
    {
        Schema::table('accountant_billings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};