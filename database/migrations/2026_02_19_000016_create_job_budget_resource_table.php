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
        Schema::create('job_budget_resource', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('resource_id')->nullable();
            $table->char('budget_version', 5)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('unit_label', 120)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('discount_percentage', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('resource_id', 'FK_resource_x_job_budget_resource')
                ->references('id')->on('resources')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_job_budget_resource')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_job_budget_resource')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('resource_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_budget_resource');
    }
};
