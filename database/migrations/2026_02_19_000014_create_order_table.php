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
        // Nota: 'order' é palavra reservada no MySQL, por isso usamos backticks via o driver.
        // O Laravel/Blueprint trata isso automaticamente.
        Schema::create('order', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->uuid('contact_id')->nullable();
            $table->uuid('organizer_id')->nullable();
            $table->uuid('status_id')->nullable();
            $table->string('order_code', 50)->nullable();
            $table->string('event_title', 255)->nullable();
            $table->string('os_type', 30)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->string('location', 255)->nullable();
            $table->text('location_details')->nullable();
            $table->uuid('job_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('customer_id', 'FK_customer_x_order')
                ->references('id')->on('customer')->nullOnDelete();
            $table->foreign('contact_id', 'FK_contact_x_order')
                ->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('organizer_id', 'FK_organizer_x_order')
                ->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('status_id', 'FK_status_x_order')
                ->references('id')->on('status')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_order')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_order')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('job_id', 'FK_job_x_order')
                ->references('id')->on('job')->nullOnDelete();

            $table->index('customer_id');
            $table->index('status_id');
            $table->index('order_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
