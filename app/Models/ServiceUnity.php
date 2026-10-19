<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceUnity extends Model
{
    use HasUuids;

    protected $table = 'service_unity';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'unity',
        'created_by',
        'updated_by'
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'unity_id');
    }
}
