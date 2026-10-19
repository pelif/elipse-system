<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource_name' => 'nullable|string|max:150',
            'name' => 'nullable|string|max:150',
            'patrimonial_code' => 'nullable|string|max:120',
            'manufacturer' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'category_id' => ['nullable', 'uuid'],
            'category' => 'nullable|string|max:255',
            'department_id' => ['nullable', 'uuid'],
            'department' => 'nullable|string|max:80',
            'responsible' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'documentation_link' => 'nullable|string|max:500',
            'search' => 'nullable|string|max:255',
            'sortField' => [
                'nullable',
                'string',
                Rule::in([
                    'id',
                    'resource_name',
                    'patrimonial_code',
                    'manufacturer',
                    'model',
                    'serial_number',
                    'notes',
                    'documentation_link',
                    'category_id',
                    'department_id',
                    'responsible_id',
                    'created_at',
                    'updated_at',
                ]),
            ],
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
