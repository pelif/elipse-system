<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComissionedAgent extends Model
{
    use HasUuids;

    protected $table = 'comissioned_agent';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'customer_id',
        'default_comission',
        'payment_days',
        'active',
        'observations',
        'created_by',
        'updated_by',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'comissioned_agent_id');
    }
}
