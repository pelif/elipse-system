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
        if (! Schema::hasTable('order')) {
            return;
        }

        Schema::table('order', function (Blueprint $table) {
            if (! Schema::hasColumn('order', 'number_participants')) {
                $table->integer('number_participants')->nullable()->default(0);
            }

            if (! Schema::hasColumn('order', 'communication_notes')) {
                $table->mediumText('communication_notes')->nullable();
            }

            if (! Schema::hasColumn('order', 'production_notes')) {
                $table->mediumText('production_notes')->nullable();
            }

            if (! Schema::hasColumn('order', 'platform_destination')) {
                $table->string('platform_destination', 255)->nullable();
            }

            if (! Schema::hasColumn('order', 'video_engine')) {
                $table->string('video_engine', 30)->nullable()->comment('Eventials, Vimeo, Zoom');
            }

            if (! Schema::hasColumn('order', 'client_platform_name')) {
                $table->string('client_platform_name', 255)->nullable();
            }

            if (! Schema::hasColumn('order', 'platform_link')) {
                $table->string('platform_link', 500)->nullable();
            }

            if (! Schema::hasColumn('order', 'file_link')) {
                $table->string('file_link', 255)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('order')) {
            return;
        }

        $columns = [
            'number_participants',
            'communication_notes',
            'production_notes',
            'platform_destination',
            'video_engine',
            'client_platform_name',
            'platform_link',
            'file_link',
        ];

        $existing = [];
        foreach ($columns as $column) {
            if (Schema::hasColumn('order', $column)) {
                $existing[] = $column;
            }
        }

        if ($existing === []) {
            return;
        }

        Schema::table('order', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }
};
