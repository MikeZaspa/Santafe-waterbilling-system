<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('disconnections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consumer_id');
            $table->unsignedBigInteger('billing_id')->nullable();
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->date('disconnection_date');
            $table->date('reconnection_date')->nullable();
            $table->enum('status', ['disconnected', 'reconnected'])->default('disconnected');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('disconnections');
    }
};

