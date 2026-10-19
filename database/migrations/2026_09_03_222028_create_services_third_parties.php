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
        if (!Schema::hasTable('services_third_parties')) {
            Schema::create('services_third_parties', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('services_id')->nullable();
                $table->uuid('third_parties_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->foreign('services_id', 'fk_service_x_third_parties')
                      ->references('id')
                      ->on('services')
                      ->nullOnDelete();

                $table->foreign('third_parties_id', 'fk_third_parties_x_services')
                      ->references('id')
                      ->on('third_parties')
                      ->nullOnDelete();

                $table->foreign('created_by', 'fk_creator_x_services_third_parties')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();

                $table->foreign('updated_by', 'fk_editor_x_services_third_parties')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('services_third_parties')) {
            Schema::dropIfExists('services_third_parties');
        }
    }

};
