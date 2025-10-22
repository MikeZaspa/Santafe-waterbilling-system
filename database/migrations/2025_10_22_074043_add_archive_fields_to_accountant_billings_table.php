<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accountant_billings', function (Blueprint $table) {
            // Add archive fields
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->string('archive_reason')->nullable();
            $table->text('archive_notes')->nullable();
            
            // Add foreign key for archived_by
            $table->foreign('archived_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('accountant_billings', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropColumn([
                'is_archived',
                'archived_at',
                'archived_by',
                'archive_reason',
                'archive_notes'
            ]);
        });
    }
};