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
        if (Schema::hasTable('customer')) {
            Schema::table('customer', function (Blueprint $table) {
                if (Schema::hasColumn('customer', 'deadline_payment')) {
                    $table->dropColumn('deadline_payment');
                }
                if (Schema::hasColumn('customer', 'deadline_type')) {
                    $table->dropColumn('deadline_type');
                }
                if (Schema::hasColumn('customer', 'antecipation')) {
                    $table->dropColumn('antecipation');
                }
                if (Schema::hasColumn('customer', 'antecipation_instrutions')) {
                    $table->dropColumn('antecipation_instrutions');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer')) {
            Schema::table('customer', function (Blueprint $table) {
                if (Schema::hasColumn('customer', 'deadline_payment')) {
                    $table->integer('deadline_payment')->nullable();
                }
                if (Schema::hasColumn('customer', 'deadline_type')) {
                    $table->string('deadline_type', 50)->nullable()->Comment('Dias úteis, Dias Corridos, Data Líquida');
                }
                if (Schema::hasColumn('customer', 'antecipation')) {
                    $table->integer('antecipation')->nullable();
                }
            });
        }
    }

};
