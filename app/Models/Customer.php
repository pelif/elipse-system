<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\ComissionedAgent;

class Customer extends Model
{
    use HasUuids;

    protected $table = 'customer';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_name',
        'legal_name',
        'document',
        'code_customer',
        'active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function financialInfo(): HasOne
    {
        return $this->hasOne(FinancialInfo::class, 'customer_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id');
    }

    public function comissionedAgent(): HasOne
    {
        return $this->hasOne(ComissionedAgent::class, 'customer_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'customer_id');
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id')
            ->where('type_contact', 'Contractor');
    }

    public function organizers(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id')
            ->where('type_contact', 'Organizer');
    }


}
