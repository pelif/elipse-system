<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialInfo extends Model
{
    use HasUuids;

    protected $table = 'financial_info';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'email_nf',
        'financial_contact_name',
        'financial_contact_phone',
        'financial_contact_email',
        'deadline_payment',
        'antecipation',
        'antecipation_instructions',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline_payment' => 'integer',
            'antecipation' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
