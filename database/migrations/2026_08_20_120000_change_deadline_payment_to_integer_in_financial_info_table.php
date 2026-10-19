<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('financial_info')) {
            return;
        }

        if (! Schema::hasColumn('financial_info', 'deadline_payment')) {
            return;
        }

        Schema::table('financial_info', function (Blueprint $table) {
            $table->integer('deadline_payment')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('financial_info')) {
            return;
        }

        if (! Schema::hasColumn('financial_info', 'deadline_payment')) {
            return;
        }

        Schema::table('financial_info', function (Blueprint $table) {
            $table->dateTime('deadline_payment')->nullable()->change();
        });
    }
};
