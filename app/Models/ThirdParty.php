<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ThirdParty extends Model
{
    use HasUuids;

    protected $table = 'third_parties';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'alias',
        'phone',
        'phone_alternative',
        'email',
        'email_alternative',
        'indication',
        'observation',
        'company_name',
        'document',
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

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'services_third_parties',
            'third_parties_id',
            'services_id'
        )->orderBy('services.service', 'asc');
    }
}
