<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ListProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'search' => 'nullable|string',
            'sortField' => 'nullable|string|in:id,name,created_at,updated_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
