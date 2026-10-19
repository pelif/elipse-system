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
        if (! Schema::hasTable('third_parties')) return;

        Schema::table('third_parties', function (Blueprint $table) {
            if (! Schema::hasColumn('third_parties', 'indication')) {
                $table->string('indication', 120)->nullable()->after('observation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('third_parties')) return;

        Schema::table('third_parties', function (Blueprint $table) {
            if (Schema::hasColumn('third_parties', 'indication')) {
                $table->dropColumn('indication');
            }
        });
    }
};
