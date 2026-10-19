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
        Schema::create('comissioned_agent', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('default_comission', 10, 2)->nullable();
            $table->integer('payment_days')->nullable();
            $table->boolean('active')->default(true);
            $table->text('observations')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('user_id', 'FK_comissioned_agent_1')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by', 'FK_comissioned_agent_2')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_comissioned_agent_3')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissioned_agent');
    }
};
