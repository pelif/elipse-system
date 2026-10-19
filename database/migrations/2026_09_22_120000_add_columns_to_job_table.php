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
        if (! Schema::hasTable('job')) {
            return;
        }

        Schema::table('job', function (Blueprint $table) {
            $this->addMissingColumns($table);
        });

        Schema::table('job', function (Blueprint $table) {
            if (Schema::hasColumn('job', 'job_code')) {
                $table->string('job_code', 20)->nullable()->change();
            }

            if (Schema::hasColumn('job', 'attachments')) {
                $table->string('attachments', 500)->nullable()->change();
            }
        });

        if (Schema::hasColumn('job', 'job_code') && ! Schema::hasIndex('job', 'job_job_code_unique', 'unique')) {
            Schema::table('job', function (Blueprint $table) {
                $table->unique('job_code', 'job_job_code_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('job')) {
            return;
        }

        Schema::table('job', function (Blueprint $table) {
            if (Schema::hasIndex('job', 'job_job_code_unique', 'unique')) {
                $table->dropUnique('job_job_code_unique');
            }

            if (Schema::hasColumn('job', 'contractor_id') && $this->foreignExistsOn('job', 'contractor_id')) {
                $table->dropForeign(['contractor_id']);
            }

            if (Schema::hasColumn('job', 'comissioned_agent_id') && $this->foreignExistsOn('job', 'comissioned_agent_id')) {
                $table->dropForeign(['comissioned_agent_id']);
            }

            $present = [];
            foreach ($this->addedColumns() as $column) {
                if (Schema::hasColumn('job', $column)) {
                    $present[] = $column;
                }
            }

            if ($present !== []) {
                $table->dropColumn($present);
            }
        });

        Schema::table('job', function (Blueprint $table) {
            if (Schema::hasColumn('job', 'job_code')) {
                $table->string('job_code', 10)->nullable()->change();
            }

            if (Schema::hasColumn('job', 'attachments')) {
                $table->string('attachments', 255)->nullable()->change();
            }
        });
    }

    private function addMissingColumns(Blueprint $table): void
    {
        if (! Schema::hasColumn('job', 'job_title')) {
            $table->string('job_title', 255)->nullable();
        }

        if (! Schema::hasColumn('job', 'os_quantity')) {
            $table->integer('os_quantity')->default(0);
        }

        if (! Schema::hasColumn('job', 'observations')) {
            $table->mediumText('observations')->nullable();
        }

        if (! Schema::hasColumn('job', 'approval_date')) {
            $table->dateTime('approval_date')->nullable();
        }

        if (! Schema::hasColumn('job', 'target_budget')) {
            $table->decimal('target_budget', 12, 2)->nullable();
        }

        if (! Schema::hasColumn('job', 'purchase_order')) {
            $table->string('purchase_order', 255)->nullable();
        }

        if (! Schema::hasColumn('job', 'purchase_order_link')) {
            $table->mediumText('purchase_order_link')->nullable();
        }

        if (! Schema::hasColumn('job', 'invoice_due_date')) {
            $table->dateTime('invoice_due_date')->nullable();
        }

        if (! Schema::hasColumn('job', 'additional_info')) {
            $table->mediumText('additional_info')->nullable();
        }

        if (! Schema::hasColumn('job', 'comissioned_agent_id')) {
            $table->uuid('comissioned_agent_id')->nullable();

            if (Schema::hasTable('comissioned_agent')) {
                $table->foreign('comissioned_agent_id', 'FK_comissioned_agent_x_job')
                    ->references('id')->on('comissioned_agent')->nullOnDelete();
            }
        }

        if (! Schema::hasColumn('job', 'commissioned_percentage')) {
            $table->decimal('commissioned_percentage', 7, 4)->nullable();
        }

        if (! Schema::hasColumn('job', 'commission_value')) {
            $table->decimal('commission_value', 12, 2)->nullable();
        }

        if (! Schema::hasColumn('job', 'commission_payment_due_date')) {
            $table->dateTime('commission_payment_due_date')->nullable();
        }

        if (! Schema::hasColumn('job', 'tax_percentage')) {
            $table->decimal('tax_percentage', 7, 4)->nullable();
        }

        if (! Schema::hasColumn('job', 'profit_percentage')) {
            $table->decimal('profit_percentage', 7, 4)->nullable();
        }

        if (! Schema::hasColumn('job', 'monthly_interest_rate')) {
            $table->decimal('monthly_interest_rate', 7, 4)->nullable();
        }

        if (! Schema::hasColumn('job', 'daily_interest_rate')) {
            $table->decimal('daily_interest_rate', 7, 4)->nullable();
        }

        if (! Schema::hasColumn('job', 'admin_expenses_percentage')) {
            $table->decimal('admin_expenses_percentage', 7, 4)->default(0);
        }

        if (! Schema::hasColumn('job', 'financial_expenses_percentage')) {
            $table->decimal('financial_expenses_percentage', 7, 4)->default(0);
        }

        if (! Schema::hasColumn('job', 'contractor_id')) {
            $table->uuid('contractor_id')->nullable();

            if (Schema::hasTable('contacts')) {
                $table->foreign('contractor_id', 'FK_contractor_x_job')
                    ->references('id')->on('contacts')->nullOnDelete();
            }
        }
    }

    /**
     * @return list<string>
     */
    private function addedColumns(): array
    {
        return [
            'job_title',
            'os_quantity',
            'observations',
            'approval_date',
            'target_budget',
            'purchase_order',
            'purchase_order_link',
            'invoice_due_date',
            'additional_info',
            'comissioned_agent_id',
            'commissioned_percentage',
            'commission_value',
            'commission_payment_due_date',
            'tax_percentage',
            'profit_percentage',
            'monthly_interest_rate',
            'daily_interest_rate',
            'admin_expenses_percentage',
            'financial_expenses_percentage',
            'contractor_id',
        ];
    }

    private function foreignExistsOn(string $table, string $column): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            $columns = $foreignKey['columns'] ?? [];
            if (in_array($column, $columns, true)) {
                return true;
            }
        }

        return false;
    }
};
