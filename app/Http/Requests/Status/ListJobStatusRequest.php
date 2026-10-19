<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;

class ListJobStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|string',
            'status' => 'nullable|string',
            'type' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'search' => 'nullable|string',
            'sortField' => 'nullable|string|in:id,status,type,observacoes',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}

