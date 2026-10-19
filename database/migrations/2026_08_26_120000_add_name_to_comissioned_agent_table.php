<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        Schema::table('comissioned_agent', function (Blueprint $table) {
            if (! Schema::hasColumn('comissioned_agent', 'name')) {
                $afterColumn = 'id';
                if (Schema::hasColumn('comissioned_agent', 'customer_id')) {
                    $afterColumn = 'customer_id';
                }

                $table->string('name')->nullable()->after($afterColumn);
            }
        });

        if (Schema::hasColumn('comissioned_agent', 'user_id') && Schema::hasColumn('comissioned_agent', 'name')) {
            $agents = DB::table('comissioned_agent')
                ->whereNotNull('user_id')
                ->get(['id', 'user_id']);

            foreach ($agents as $agent) {
                $user = DB::table('users')
                    ->where('id', $agent->user_id)
                    ->first(['name']);

                if ($user !== null) {
                    DB::table('comissioned_agent')
                        ->where('id', $agent->id)
                        ->update(['name' => $user->name]);
                }
            }
        }
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
            if (Schema::hasColumn('comissioned_agent', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
