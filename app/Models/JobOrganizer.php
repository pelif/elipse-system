<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class JobOrganizer extends Pivot
{
    protected $table = 'job_organizer';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'organizer_id',
        'created_at',
    ];
}
