<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasUuids;

    protected $table = 'job';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'product_id',
        'job_code',
        'attachments',
        'approval_ar',
        'status_id',
        'created_by',
        'updated_by',
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

    protected function casts(): array
    {
        return [
            'approval_ar' => 'datetime',
            'approval_date' => 'datetime',
            'invoice_due_date' => 'datetime',
            'commission_payment_due_date' => 'datetime',
            'os_quantity' => 'integer',
            'target_budget' => 'decimal:2',
            'commission_value' => 'decimal:2',
            'commissioned_percentage' => 'decimal:4',
            'tax_percentage' => 'decimal:4',
            'profit_percentage' => 'decimal:4',
            'monthly_interest_rate' => 'decimal:4',
            'daily_interest_rate' => 'decimal:4',
            'admin_expenses_percentage' => 'decimal:4',
            'financial_expenses_percentage' => 'decimal:4',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function comissionedAgent(): BelongsTo
    {
        return $this->belongsTo(ComissionedAgent::class, 'comissioned_agent_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contractor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'job_id');
    }

    public function organizers(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'job_organizer',
            'job_id',
            'organizer_id'
        )->using(JobOrganizer::class);
    }
}
