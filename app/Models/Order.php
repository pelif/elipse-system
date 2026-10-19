<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasUuids;

    protected $table = 'order';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'customer_id',
        'contact_id',
        'organizer_id',
        'status_id',
        'order_code',
        'event_title',
        'os_type',
        'start_time',
        'end_time',
        'duration',
        'location',
        'location_details',
        'number_participants',
        'communication_notes',
        'production_notes',
        'platform_destination',
        'video_engine',
        'client_platform_name',
        'platform_link',
        'file_link',
        'job_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'number_participants' => 'integer',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'organizer_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
