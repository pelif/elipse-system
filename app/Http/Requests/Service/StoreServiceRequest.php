<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service'     => 'required|string|max:255',
            'unity_id'    => 'required|uuid|exists:service_unity,id',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'service.required'  => 'O campo serviço é obrigatório.',
            'service.max'       => 'O campo serviço deve ter no máximo 255 caracteres.',
            'unity_id.required' => 'O campo unidade é obrigatório.',
            'unity_id.uuid'     => 'O identificador da unidade deve ser um UUID válido.',
            'unity_id.exists'   => 'Unidade de serviço não encontrada.',
            'description.max'   => 'O campo descrição deve ter no máximo 500 caracteres.',
        ];
    }
}
