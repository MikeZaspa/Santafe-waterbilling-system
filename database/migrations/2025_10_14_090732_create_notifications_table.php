<?php
// database/migrations/xxxx_xx_xx_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('consumer_notifications', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('message');
        $table->string('type')->default('info');
        $table->unsignedBigInteger('consumer_id');
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });

    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}