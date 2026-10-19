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

        if (! Schema::hasTable('comissioned_agent')) return;

        Schema::table('comissioned_agent', function (Blueprint $table) {
            if (! Schema::hasColumn('comissioned_agent', 'customer_id')) {
                $table->uuid('customer_id')->nullable()->after('id');
                $table->foreign('customer_id')->references('id')->on('customer')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('comissioned_agent')) return;

            Schema::table('comissioned_agent', function (Blueprint $table) {
            if (Schema::hasColumn('comissioned_agent', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
