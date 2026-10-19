<?php

namespace App\Http\Requests\ResourceCategory;

use Illuminate\Foundation\Http\FormRequest;

class ListResourceCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
            'sortField' => 'nullable|string|in:id,name,description',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
