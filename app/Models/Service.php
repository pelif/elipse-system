<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasUuids;

    protected $table = 'services';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'service',
        'description',
        'unity_id',
        'created_by',
        'updated_by',
    ];

    public function unity(): BelongsTo
    {
        return $this->belongsTo(ServiceUnity::class, 'unity_id');
    }

    public function thirdParties(): BelongsToMany
    {
        return $this->belongsToMany(
            ThirdParty::class,
            'services_third_parties',
            'services_id',
            'third_parties_id'
        );
    }
}
