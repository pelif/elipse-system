<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('financial_info', 'deadline_type')) {
            Schema::table('financial_info', function (Blueprint $table) {
                $table->dropColumn('deadline_type');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('financial_info', 'deadline_type')) {
            Schema::table('financial_info', function (Blueprint $table) {
                $table->string('deadline_type', 50)->nullable()->comment('Dias úteis, Dias Corridos, Data Líquida');
            });
        }
    }
};
