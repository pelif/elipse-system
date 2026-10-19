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
        Schema::create('customer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name', 255)->nullable();
            $table->string('legal_name', 255)->nullable();
            $table->string('document', 255)->nullable();
            $table->char('code_customer', 10)->nullable();
            $table->boolean('active')->default(true);
            $table->integer('deadline_payment')->nullable();
            $table->string('deadline_type', 50)->nullable()->Comment('Dias úteis, Dias Corridos, Data Líquida');
            $table->integer('antecipation')->nullable();
            $table->string('antecipation_instrutions', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by', 'FK_customer_1')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_customer_2')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('code_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
