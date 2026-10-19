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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->string('name', 160)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('type_contact', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('customer_id', 'fk_customer_x_contacts')
                ->references('id')->on('customer')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_contacts')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_contacts')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
