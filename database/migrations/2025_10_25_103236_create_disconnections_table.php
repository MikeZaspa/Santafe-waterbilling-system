<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisconnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disconnections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consumer_id');
            $table->unsignedBigInteger('billing_id');
            $table->string('name');
            $table->string('consumer_type')->nullable();
            $table->string('meter_no')->nullable();
            $table->decimal('previous_reading', 10, 2)->default(0);
            $table->decimal('current_reading', 10, 2)->default(0);
            $table->decimal('consumption', 10, 2)->default(0);
            $table->date('reading_date')->nullable();
            $table->string('reason');
            $table->date('disconnection_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('disconnected_by')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disconnections');
    }
}