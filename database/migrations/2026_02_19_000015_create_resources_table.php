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
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id')->nullable();
            $table->uuid('department_id')->nullable();
            $table->string('resource_name', 150)->nullable();
            $table->string('patrimonial_code', 120)->nullable();
            $table->string('manufacturer', 150)->nullable();
            $table->string('model', 255)->nullable();
            $table->string('serial_number', 255)->nullable();
            $table->unsignedBigInteger('responsible_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('documentation_link', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('category_id', 'FK_category_x_resources')
                ->references('id')->on('resource_category')->nullOnDelete();
            $table->foreign('responsible_id', 'FK_responsible_x_resources')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_resources')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_resources')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('department_id', 'FK_resources_x_department')
                ->references('id')->on('department')->nullOnDelete();

            $table->index('category_id');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
