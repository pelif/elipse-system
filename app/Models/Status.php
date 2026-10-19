<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasUuids;

    protected $table = 'status';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'status',
        'type',
        'observacoes',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'status_id');
    }
}
