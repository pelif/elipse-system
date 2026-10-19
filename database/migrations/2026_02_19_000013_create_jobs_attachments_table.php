<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_id')->nullable();
            $table->string('filename', 255)->nullable();
            $table->string('path', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('job_id', 'FK_job_x_attachments')
                ->references('id')->on('job')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_attachments')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_attachments')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs_attachments');
    }
};
