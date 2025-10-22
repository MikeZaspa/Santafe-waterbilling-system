<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cut_consumers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consumer_id');
            $table->unsignedBigInteger('billing_id');
            $table->string('name');
            $table->string('consumer_type');
            $table->string('meter_no');
            $table->string('reason');
            $table->date('cut_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('cut_by');
            $table->json('billing_data')->nullable(); // Add this field to store billing data
            $table->timestamps();
         
            $table->index('cut_date');
            $table->index('consumer_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cut_consumers');
    }
};