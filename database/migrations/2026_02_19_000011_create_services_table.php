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
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unity_id')->nullable();
            $table->string('service', 255)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('unity_id', 'FK_unity_x_services')
                ->references('id')->on('service_unity')->nullOnDelete();
            $table->foreign('created_by', 'FK_creator_x_services')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by', 'FK_editor_x_services')
                ->references('id')->on('users')->nullOnDelete();

            $table->index('unity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
