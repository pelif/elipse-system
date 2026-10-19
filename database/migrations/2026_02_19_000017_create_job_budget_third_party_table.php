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
        Schema::create('job_budget_third_party', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_id')->nullable();
            $table->uuid('third_party_id')->nullable();
            $table->uuid('service_id')->nullable();
            $table->char('budget_version', 5)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('discount_percentage', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('job_id', 'FK_job_x_job_budget_third_party')
                ->references('id')->on('job')->nullOnDelete();
            $table->foreign('third_party_id', 'FK_third_party_x_job_budget_third_party')
                ->references('id')->on('third_parties')->nullOnDelete();
            $table->foreign('service_id', 'FK_service_x_job_budget_third_party')
                ->references('id')->on('services')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_job_budget_third_party')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_job_budget_third_party')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('job_id');
            $table->index('third_party_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_budget_third_party');
    }
};
