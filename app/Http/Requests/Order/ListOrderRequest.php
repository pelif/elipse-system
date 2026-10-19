<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class ListOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_code' => 'nullable|string|max:50',
            'os_code' => 'nullable|string|max:50',
            'osCode' => 'nullable|string|max:50',
            'job_code' => 'nullable|string|max:20',
            'jobCode' => 'nullable|string|max:20',
            'event_title' => 'nullable|string|max:255',
            'eventTitle' => 'nullable|string|max:255',
            'customer' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'organizerName' => 'nullable|string|max:255',
            'sortField' => 'nullable|string|in:id,order_code,event_title,start_time,created_at,updated_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
