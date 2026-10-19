<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ListUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'role' => 'nullable|string',
            'createdAt' => 'nullable|string',
            'sortField' => 'nullable|string|in:name,email,role,createdAt',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}

