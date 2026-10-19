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
        if (! Schema::hasTable('financial_info')) {
            return;
        }

        Schema::table('financial_info', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_info', 'financial_contact_name')) {
                $table->string('financial_contact_name', 255)->nullable()->after('email_nf');
            }
            if (! Schema::hasColumn('financial_info', 'financial_contact_phone')) {
                $table->string('financial_contact_phone', 50)->nullable();
            }
            if (! Schema::hasColumn('financial_info', 'financial_contact_email')) {
                $table->string('financial_contact_email', 255)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('financial_info')) {
            return;
        }

        Schema::table('financial_info', function (Blueprint $table) {
            if (Schema::hasColumn('financial_info', 'financial_contact_email')) {
                $table->dropColumn('financial_contact_email');
            }
            if (Schema::hasColumn('financial_info', 'financial_contact_phone')) {
                $table->dropColumn('financial_contact_phone');
            }
            if (Schema::hasColumn('financial_info', 'financial_contact_name')) {
                $table->dropColumn('financial_contact_name');
            }
        });
    }
};
