<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accountant_billings', function (Blueprint $table) {
            $table->decimal('penalty_amount', 10, 2)->default(0.00)->after('total_amount');
        });
    }

    public function down()
    {
        Schema::table('accountant_billings', function (Blueprint $table) {
            $table->dropColumn('penalty_amount');
        });
    }
};
