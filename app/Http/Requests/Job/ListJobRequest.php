<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class ListJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer' => 'nullable|string|max:255',
            'customer_id' => 'nullable|uuid',
            'job_code' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'product' => 'nullable|string|max:255',
            'product_id' => 'nullable|uuid',
            'status' => 'nullable|string|max:255',
            'status_id' => 'nullable|uuid',
            'contractor' => 'nullable|string|max:255',
            'contractor_id' => 'nullable|uuid',
            'created_at' => 'nullable|string|max:20',
            'updated_at' => 'nullable|string|max:20',
            'sortField' => 'nullable|string|in:id,job_code,job_title,os_quantity,created_at,updated_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
