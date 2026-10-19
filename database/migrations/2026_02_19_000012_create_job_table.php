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
        Schema::create('job', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->uuid('product_id')->nullable();
            $table->string('job_code', 10)->nullable();
            $table->string('attachments', 255)->nullable();
            $table->dateTime('approval_ar')->nullable();
            $table->uuid('status_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('customer_id', 'FK_customer_x_job')
                ->references('id')->on('customer')->nullOnDelete();
            $table->foreign('product_id', 'FK_product_x_job')
                ->references('id')->on('products')->nullOnDelete();
            $table->foreign('status_id', 'FK_status_x_job')
                ->references('id')->on('status')->nullOnDelete();
            $table->foreign('created_by', 'FK_created_by_x_job')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_updated_by_x_job')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('customer_id');
            $table->index('status_id');
            $table->index('job_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job');
    }
};
