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
        if (Schema::hasTable('job_organizer')) {
            return;
        }

        Schema::create('job_organizer', function (Blueprint $table) {
            $table->uuid('job_id');
            $table->uuid('organizer_id');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['job_id', 'organizer_id']);

            $table->foreign('job_id', 'fk_job_x_job_organizer')
                ->references('id')->on('job')->cascadeOnDelete();
            $table->foreign('organizer_id', 'fk_organizer_x_job_organizer')
                ->references('id')->on('contacts')->cascadeOnDelete();

            $table->index('organizer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_organizer');
    }
};
