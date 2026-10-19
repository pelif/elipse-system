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
        if (! Schema::hasTable('comissioned_agent')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        Schema::table('comissioned_agent', function (Blueprint $table) use ($driver) {
            if (Schema::hasColumn('comissioned_agent', 'user_id')) {
                if ($driver === 'sqlite') {
                    $table->dropForeign(['user_id']);
                } else {
                    $table->dropForeign('FK_comissioned_agent_1');
                }

                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('comissioned_agent')) {
            return;
        }

        Schema::table('comissioned_agent', function (Blueprint $table) {
            if (! Schema::hasColumn('comissioned_agent', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');

                $table->foreign('user_id', 'FK_comissioned_agent_1')
                    ->references('id')->on('users')->nullOnDelete();

                $table->index('user_id');
            }
        });
    }
};
