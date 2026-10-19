<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ServiceThirdParty extends Model
{
    use HasUuids;

    protected $table = 'services_third_parties';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'services_id',
        'third_parties_id',
        'created_by',
        'updated_by',
    ];
}
