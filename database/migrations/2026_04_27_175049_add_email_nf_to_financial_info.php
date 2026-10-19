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
        if (Schema::hasTable('financial_info')) {
            Schema::table('financial_info', function (Blueprint $table) {
                $table->string('email_nf', 255)->nullable()->after('customer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_info', function (Blueprint $table) {
            if (Schema::hasColumn('financial_info', 'email_nf')) {
                $table->dropColumn('email_nf');
            }
        });
    }
};
