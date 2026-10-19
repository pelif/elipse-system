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
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table): void {
            if (! Schema::hasColumn('contacts', 'communication_care')) {
                $table->text('communication_care')->nullable()->after('email');
            }
        });

        Schema::table('contacts', function (Blueprint $table): void {
            if (! Schema::hasColumn('contacts', 'observations')) {
                $afterColumn = 'email';

                if (Schema::hasColumn('contacts', 'communication_care')) {
                    $afterColumn = 'communication_care';
                }

                $table->text('observations')->nullable()->after($afterColumn);
            }
        });
    }

    /**
     * Reverse the migrations.
     * Does not drop communication_care — that lifecycle belongs to
     * add_contractor_fields_to_contacts_table rollback.
     */
    public function down(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table): void {
            if (Schema::hasColumn('contacts', 'observations')) {
                $table->dropColumn('observations');
            }
        });
    }
};
