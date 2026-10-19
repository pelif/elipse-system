<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class ListServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service'        => 'nullable|string',
            'created_at'     => 'nullable|string',
            'updated_at'     => 'nullable|string',
            'search'         => 'nullable|string',
            'sortField'      => 'nullable|string|in:id,service,created_at,updated_at',
            'sortDirection'  => 'nullable|string|in:asc,desc',
            'page'           => 'nullable|integer|min:1',
        ];
    }
}
