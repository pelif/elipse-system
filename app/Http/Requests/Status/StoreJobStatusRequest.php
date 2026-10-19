<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|max:50',
            'observacoes' => 'required|string|max:500',
            'type' => 'nullable|integer|in:1',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O campo status é obrigatório.',
            'status.max' => 'O campo status deve ter no máximo 50 caracteres.',
            'observacoes.max' => 'O campo observações deve ter no máximo 500 caracteres.',
            'observacoes.required' => 'O campo observações é obrigatório.',
        ];
    }
}

