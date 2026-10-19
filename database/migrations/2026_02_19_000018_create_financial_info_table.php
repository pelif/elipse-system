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
        Schema::create('financial_info', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->integer('deadline_payment')->nullable();
            $table->string('deadline_type', 50)->nullable()->Comment('Dias úteis, Dias Corridos, Data Líquida');
            $table->integer('antecipation')->nullable();
            $table->string('antecipation_instructions', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('customer_id', 'FK_customer_x_financial_info')
                ->references('id')->on('customer')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_financial_info')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_financial_info')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_info');
    }
};
