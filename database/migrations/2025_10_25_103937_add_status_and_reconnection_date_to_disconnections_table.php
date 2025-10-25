<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndReconnectionDateToDisconnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disconnections', function (Blueprint $table) {
            $table->enum('status', ['disconnected', 'reconnected'])->default('disconnected')->after('notes');
            $table->date('reconnection_date')->nullable()->after('status');
            $table->decimal('reconnection_fee', 8, 2)->default(500.00)->after('reconnection_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disconnections', function (Blueprint $table) {
            $table->dropColumn(['status', 'reconnection_date', 'reconnection_fee']);
        });
    }
}